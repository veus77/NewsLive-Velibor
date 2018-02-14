<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/5/17
 * Time: 1:03 PM
 */

namespace Drupal\videos_front\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "videos_front",
 *   admin_label = @Translation("Videos Front Block"),
 *   category = @Translation("other"),
 * )
 */

class VideosFrontBlock extends BlockBase
{
    private function buildContent()
    {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'video');
        $query->condition('nfd.status', '1', '=');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'nid');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttf',
            'ttf.tid = ti.tid');
        $query->condition('ttf.name', 'Video', '=');

        $query->addField('ttf', 'name', 'taxonomy');

        $query->innerJoin('node__field_video_image', 'fiv',
            'nfd.nid = fiv.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fiv.field_video_image_target_id');
        $query->addField('fm', 'uri', 'image');

//        $query->orderBy( 'nfd.nid', 'DESC' );

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 4 );

        $results = $query->execute()->fetchAll();
        $data = [];

        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('video_header_image')->buildUrl($image);
            $path = $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$path);
            $data[] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0, 49),
                'taxonomy' => $result->taxonomy,
            ];
        }

        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'videos_front',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}