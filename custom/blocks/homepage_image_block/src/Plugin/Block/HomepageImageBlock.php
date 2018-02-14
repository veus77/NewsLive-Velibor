<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/4/17
 * Time: 12:56 PM
 */

namespace Drupal\homepage_image_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "homepage_image_block",
 *   admin_label = @Translation("Homepage Image Block"),
 *   category = @Translation("other"),
 * )
 */

class HomepageImageBlock extends BlockBase
{
    private function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'news', '=');
        $query->condition('n.status', '1', '=');
        $query->addField('n', 'title');
        $query->addField('n', 'nid');
        $query->addField('n', 'created');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = n.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttfd',
            'ttfd.tid = ti.tid');
        $query->condition('ttfd.vid', 'technology', '=');

        $query->addField('ttfd', 'name', 'taxonomy');

        $query->innerJoin('node__field_header_image', 'fi',
            'n.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'file',
            'file.fid = fi.field_header_image_target_id'    );
        $query->addField('file', 'uri', 'image');

        $query->orderBy( 'n.nid', 'DESC' );


        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );
        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {

            $image=$result->image;
            if ($data[0]) {
	            $url = \Drupal\image\Entity\ImageStyle::load('home_image_block_bottom')->buildUrl($image);
            } else {
	            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            }
            $path = '/node/' . $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);
            $data[] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)).'...', 0, 37),
                'taxonomy' => $result->taxonomy,
                'created' => date('M d, Y', $result->created),
            ];
        }
        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'homepage_image_block',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }

}