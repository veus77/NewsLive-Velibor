<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/4/17
 * Time: 9:38 AM
 */

namespace Drupal\homepage_slider\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "homepage_slider",
 *   admin_label = @Translation("Homepage Slider"),
 *   category = @Translation("other"),
 * )
 */

class HomepageSlider extends BlockBase
{
    private function buildContent()
    {
//        $query=\Drupal::database()->select('node_field_data', 'nfd');
//        $query->condition('type', 'video');
//        $query->condition('nfd.status', '1', '=');
//        $query->addField('nfd', 'title');
//        $query->addField('nfd', 'nid');

        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'news', '=');
        $query->condition('n.status', '1', '=');
        $query->addField('n', 'nid');
        $query->addField('n', 'title');
        $query->addField('n', 'created');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = n.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttfd',
            'ttfd.tid = ti.tid');
        $query->condition('ttfd.vid', 'lifestyles_taxonomy', '=');

        $query->addField('ttfd', 'name', 'taxonomy');

        $query->innerJoin('node__field_header_image', 'fi',
            'n.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'file',
            'file.fid = fi.field_header_image_target_id');
        $query->addField('file', 'uri', 'image');

        $query->orderBy( 'n.nid', 'DESC' );


        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 6 );
        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $path = '/node/' . $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);
            $data[] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)).'...', 0, 37),
                'taxonomy' => $result->taxonomy,
                'created' => date('F d, Y', $result->created),
            ];
        }
        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'homepage_slider',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }

}