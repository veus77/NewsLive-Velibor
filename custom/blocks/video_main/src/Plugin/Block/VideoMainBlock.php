<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/1/17
 * Time: 9:39 AM
 */

namespace Drupal\video_main\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "video_main",
 *   admin_label = @Translation("Video Main"),
 *   category = @Translation("other"),
 * )
 */

class VideoMainBlock extends  BlockBase implements BlockPluginInterface
{
    private function buildContent()
    {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'video');
        $query->condition('nfd.status', '1', '=');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'nid');

        $query->orderBy( 'nid', 'DESC' );

        $query->innerJoin('node__field_video_image', 'nfv',
            'nfd.nid = nfv.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = nfv.field_video_image_target_id');
        $query->addField('fm', 'uri', 'image');

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 4 );
        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('medium')->buildUrl($image);
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)).'...', 0, 37),
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