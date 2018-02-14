<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/30/17
 * Time: 11:55 AM
 */

namespace Drupal\popular_news_front\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "popular_news_front",
 *   admin_label = @Translation("Popular News Front"),
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

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttfd',
            'ttfd.tid = ti.tid');
        $query->condition('ttfd.vid', 'technology', '=');

        $query->addField('ttfd', 'name', 'taxonomy');

        $query->innerJoin('users_field_data', 'u',
            'nfd.uid = u.uid');
        $query->addField('u', 'name', 'user');

        $query->innerJoin('node__body', 'b',
            'b.entity_id = nfd.nid');
        $query->addField('b', 'body_value', 'body');

        $query->innerJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fi.field_header_image_target_id');
        $query->addField('fm', 'uri', 'image');

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 5 );

        $results = $query->execute()->fetchAll();
        $data = [];


        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('popular_news_370x260_')->buildUrl($image);
            $path =  $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$path);
            $data['technology'][] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0, 56),
                'taxonomy' => $result->taxonomy,
                'created' => date('M d, Y', $result->created),
                'user' => $result->user,
                'body' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->body)), 0, 151).'...',
            ];
        }

//        return $data;

        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'news');
        $query->condition('nfd.status', '1', '=');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');
        $query->addField('nfd', 'nid');

        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttfd',
            'ttfd.tid = ti.tid');
        $query->condition('ttfd.vid', 'lifestyles_taxonomy', '=');

        $query->addField('ttfd', 'name', 'taxonomy');

        $query->innerJoin('users_field_data', 'u',
            'nfd.uid = u.uid');
        $query->addField('u', 'name', 'user');

        $query->innerJoin('node__body', 'b',
            'b.entity_id = nfd.nid');
        $query->addField('b', 'body_value', 'body');


        $query->innerJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fi.field_header_image_target_id');
        $query->addField('fm', 'uri', 'image');

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 5 );

        $results = $query->execute()->fetchAll();
        //$data = [];

        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('medium')->buildUrl($image);
            $path = $result->nid;
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$path);
            $data['lifestyles'][] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)), 0, 56),
                'taxonomy' => $result->taxonomy,
                'created' => date('M d, Y', $result->created),
                'user' => $result->user,
                'body' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->body)), 0, 151).'...',
            ];
        }




        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'popular_news_front',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }

}