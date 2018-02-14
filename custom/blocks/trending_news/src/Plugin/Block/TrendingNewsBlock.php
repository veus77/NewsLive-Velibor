<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/7/17
 * Time: 4:34 PM
 */

namespace Drupal\trending_news\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "trending_news",
 *   admin_label = @Translation("Trending News"),
 *   category = @Translation("other"),
 * )
 */

class TrendingNewsBlock extends BlockBase
{
    private function buildContent()
    {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', 'news');
        $query->condition('nfd.status', '1', '=');
        $query-> distinct();
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');
        $query->addField('nfd', 'nid');

        $query->orderBy( 'nid', 'DESC' );

        $query->innerJoin('node__field_header_image', 'fi',
            'nfd.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'fm',
            'fm.fid = fi.field_header_image_target_id');
        $query->addField('fm', 'uri', 'image');

        $query->range('0','6');
        $results = $query->execute()->fetchAll();

        $data = [];

        foreach ($results as $result) {

            $query=\Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.nid', $result->nid, '=');

            $query->leftJoin('taxonomy_term_field_data', 'ttf',
                'ttf.tid=ti.tid');

            $query->addField('ttf', 'name', 'taxonomy');

            $taxonomy = $query->execute()->fetchField();


            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);

            $_data[] = [
                'nid'=>$alias,
                'image' => $url,
                'title' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->title)).'...', 0, 37),
                'created' => date('M d, Y',$result->created),
                'taxonomy'=> $taxonomy,
            ];

            if(count($_data)>=2){
                $data[]=$_data;
                $_data=[];
            }
        }

        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'trending_news',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }

}