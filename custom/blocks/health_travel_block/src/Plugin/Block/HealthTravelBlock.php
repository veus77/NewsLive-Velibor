<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/5/17
 * Time: 9:56 AM
 */

namespace Drupal\health_travel_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "health_travel_block",
 *   admin_label = @Translation("Health Travel Block"),
 *   category = @Translation("other"),
 * )
 */

class HealthTravelBlock extends  BlockBase
{
    private function buildContent()
    {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'news');
        $query->condition('nfd.status', '1', '=');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');
        $query->addField('nfd', 'nid');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttf',
            'ttf.tid = ti.tid');
        $query->condition('ttf.name', 'Health', '=');

//        $query->addField('ttf','name', 'taxonomy');

        $query->innerJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fi.field_header_image_target_id');
        $query->addField('fm', 'uri', 'image');

//        $query->orderBy( 'nfd.nid', 'DESC' );

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 5 );

        $results = $query->execute()->fetchAll();
        $data = [];


        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $path =$result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$path);
            $data['health'][] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0),
                'created' => date('M d, Y',$result->created),
            ];
        }


        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'news');
        $query->condition('nfd.status', '1', '=');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');
        $query->addField('nfd', 'nid');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttf',
            'ttf.tid = ti.tid');
        $query->condition('ttf.name', 'Travel', '=');

        $query->addField('ti', 'tid');

        $query->innerJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fi.field_header_image_target_id');
        $query->addField('fm', 'uri', 'image');

//        $query->orderBy( 'nfd.nid', 'DESC' );

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 5 );

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $pathTaxonomy=$result->tid;
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $path =  $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$path);
//            $alias2 = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$pathTaxonomy);
            $data['travel'][] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0, 48).'...',
                'created' => date('M d, Y',$result->created),
            ];
        }

        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'health_travel_block',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}