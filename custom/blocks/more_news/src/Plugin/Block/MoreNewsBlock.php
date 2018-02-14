<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/5/17
 * Time: 2:10 PM
 */

namespace Drupal\more_news\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Query\Condition;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "more_news",
 *   admin_label = @Translation("More News"),
 *   category = @Translation("other"),
 * )
 */

class MoreNewsBlock extends BlockBase
{
    private function buildContent()
    {
        $query=\Drupal::database()->select('node_field_data', 'nfd');
        $query->condition('type', ['news', 'video'], 'IN');

        $query->condition('nfd.status', '1', '=');
        $query->distinct();
        $query->addField('nfd', 'nid');
        $query->addField('nfd', 'nid', 'id');
        $query->addField('nfd', 'title');
        $query->addField('nfd', 'created');


        $query->leftJoin('taxonomy_index', 'ti',
            'ti.nid = nfd.nid');
        $query->leftJoin('taxonomy_term_field_data', 'ttf',
            'ttf.tid = ti.tid');

        $query->addField('ti', 'tid');

        $query->addField('ttf', 'name', 'taxonomy');


        $query->leftJoin('node__body', 'b',
            'b.entity_id = nfd.nid');
        $query->addField('b', 'body_value', 'body');

        $query->leftJoin('users_field_data', 'u',
            'nfd.uid = u.uid');
        $query->addField('u', 'name', 'user');

        $query->leftJoin('node__field_video_image', 'fiv',
            'nfd.nid = fiv.entity_id');
        $query->leftJoin('file_managed', 'fm',
            'fm.fid = fiv.field_video_image_target_id');
        $query->addField('fm', 'uri', 'video_image');

        $query->leftJoin('node__field_header_image', 'fi',
                'nfd.nid = fi.entity_id');

        $query->leftJoin('file_managed', 'fim',
            'fim.fid = fi.field_header_image_target_id');
        $query->addField('fim', 'uri', 'header_image');
        $query->orderBy( 'nfd.nid', 'DESC' );

        $results = $query->execute()->fetchAll(\PDO::FETCH_GROUP  );
        $data = [];


        foreach ($results as $result) {
$_data = [];
            if(Count($data)>3){
                break;
            }
            foreach($result as $node ) {

                $path = $node->id;
                $pathTaxonomy=$node->tid;
                $image=$node->header_image;
                $image2=$node->video_image;

                $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' .$path);
                $alias2 = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$pathTaxonomy);

                $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
                $url2 = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image2);

                $_data['nid'] = $alias;
                $_data['title'] = substr(strip_tags(str_replace(array("\r", "\n"), '', $node->title)), 0);
                $_data['created'] = date('F d, Y', $node->created);
//                $_data['taxonomy'][] = [
//                    'name'  => strip_tags($node->taxonomy),
//                    'url'   => $alias2
//                ];
                $_data['taxonomy']=$node->taxonomy;
                $_data['body'] = substr(strip_tags(str_replace(array("\r", "\n"), '', $node->body)), 0, 192);
                $_data['user'] = strip_tags($node->user);
                if( !empty( $node->video_image)) {
                    $_data['video_image'] = $url2;
                }
                if( !empty( $node->header_image)) {
                    $_data['header_image'] = $url;
                }
            }
            $data[] = $_data;
        }

        return $data;
    }

    public function build()
    {
        return array(
            '#theme'    => 'more_news',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}