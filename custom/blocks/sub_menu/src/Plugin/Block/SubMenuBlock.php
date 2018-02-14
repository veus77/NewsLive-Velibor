<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/1/17
 * Time: 12:42 PM
 */

namespace Drupal\sub_menu\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "sub_menu_first",
 *   admin_label = @Translation("Sub-first Menu Block"),
 *   category = @Translation("Custom"),
 * )
 */

class SubMenuBlock extends BlockBase {
    private function buildContent() {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('nfd.type', 'news', '=');
        $query->condition('nfd.status', '1', '=');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttf',
            'ttf.tid = ti.tid');

        $query->condition('ttf.name', 'Health', '=');

        $query->addField('ttf', 'name', 'taxonomy');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'nid');

//        $query->orderBy( 'nid', 'DESC' );

        $query->innerJoin('node__field_header_image', 'nfh',
            'nfd.nid = nfh.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = nfh.field_header_image_target_id');
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
                'taxonomy' => $result->taxonomy,
            ];
        }
        return $data;

    }

    public function build()
    {
        return array(
            '#theme'    => 'sub_menu',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}