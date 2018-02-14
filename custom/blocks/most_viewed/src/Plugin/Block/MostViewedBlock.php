<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/6/17
 * Time: 1:30 PM
 */

namespace Drupal\most_viewed\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "most_viewed",
 *   admin_label = @Translation("Most Viewed"),
 *   category = @Translation("other"),
 * )
 */
class MostViewedBlock extends BlockBase
{
    private function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'news');

        $query->condition('nfd.status', '1', '=');

        $query->addField('nfd', 'nid');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');

        $query->leftJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->leftJoin('file_managed', 'fim',
            'fim.fid = fi.field_header_image_target_id');

        $query->innerJoin('node_counter', 'nc',
            'nfd.nid = nc.nid');
        $query->orderBy('totalcount', 'DESC');

        $query->addField('fim', 'uri', 'header_image');
        $query->range('0', '5');

        $results = $query->execute()->fetchAll();
        $data = [];


        foreach ($results as $result) {

            $query = \Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.nid', $result->nid, '=');
            $query->innerJoin('taxonomy_term_field_data', 'ttf',
                'ttf.tid = ti.tid');
            $query->addField('ttf', 'name', 'taxonomy');
            $query->range(0, 1);
            $taxonomy = $query->execute()->fetchField();

            $path=$result->nid;
            $image = $result->header_image;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $image);
            $alias2 = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $path);
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $data[]= [
                'nid' => $alias2,
                'title' =>  substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0),
                'created' =>date('M d, Y', $result->created),
                'taxonomy'=> $taxonomy,
                'header_image' => $url,

            ];
        }

        return $data;
    }

    public function build()
    {
        return array(
            '#theme' => 'most_viewed',
            '#content' => $this->buildContent(),
            '#cache' => [
                'max-age' => 0,
            ],
        );
    }
}