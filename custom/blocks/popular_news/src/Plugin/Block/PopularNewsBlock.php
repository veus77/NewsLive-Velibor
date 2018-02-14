<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/30/17
 * Time: 11:55 AM
 */

namespace Drupal\popular_news\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "popular_news",
 *   admin_label = @Translation("Popular News"),
 *   category = @Translation("other"),
 * )
 */
class PopularNewsBlock extends BlockBase implements BlockPluginInterface
{
    private function buildContent()
    {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'news');
        $query->condition('nfd.status', '1', '=');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');
        $query->addField('nfd', 'nid');


        $query->innerJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fi.field_header_image_target_id');
        $query->addField('fm', 'uri', 'image');

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );
        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $image=$result->image;
            $path=$result->nid;
            $url = \Drupal\image\Entity\ImageStyle::load('medium')->buildUrl($image);
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$path);
            $data[] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0, 36),
//                'title' => $result->title,
                'created' => date('M d, Y',$result->created),
            ];
        }

        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'news_live',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }

}