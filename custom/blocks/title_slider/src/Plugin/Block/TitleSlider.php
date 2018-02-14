<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/11/17
 * Time: 10:00 AM
 */

namespace Drupal\title_slider\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "title_slider",
 *   admin_label = @Translation("Title slider"),
 *   category = @Translation("other"),
 * )
 */

class TitleSlider extends BlockBase
{
    private function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'news', '=');
        $query->condition('n.status', '1', '=');
        $query->addField('n', 'title');
        $query->addField('n', 'nid');
        $query->orderBy( 'n.nid', 'DESC' );

        $data = [];

        $results = $query->execute()->fetchAll();


        foreach ($results as $result) {
            $path = '/node/' . $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);
            $data[] = [
                'nid'=>$alias,
                'title' => $result->title,
            ];
        }
        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'title_slider',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }

}