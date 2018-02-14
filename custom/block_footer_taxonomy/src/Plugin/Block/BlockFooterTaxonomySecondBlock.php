<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/28/17
 * Time: 4:37 PM
 */

namespace Drupal\block_footer_taxonomy\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
 * Provides a custom block
 *
 * @Block(
 *   id = "blockFooterSecond taxonomy",
 *   admin_label = @Translation("Footer taxonomy"),
 *   category = @Translation("Other"),
 * )
 */

class BlockFooterTaxonomySecondBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $query = \Drupal::database()->select('taxonomy_index','t' );
        $query->leftJoin('taxonomy_term_field_data', 'tf', 't.tid = tf.tid');
        $query->condition('tf.vid', 'lifestyles_taxonomy', '=');
        $query->addExpression('COUNT(t.nid)', 'cnt' );
        $query->addExpression('tf.name', 'name' );
        $query->groupBy('name' );
        $results = $query->execute()->fetchAll();
        $life = '<div class="bottom"><ul>';
        foreach($results as $term => $value)
        {
            $life .='<li>';
            $life .= '<a href="'.$value->name.'">'.$value->name.'<span>('.$value->cnt.')</span>'.'</a>';
            $life .='</li>';
        }
        $life .= '</ul></div>';

        $query = \Drupal::database()->select('taxonomy_index','t' );
        $query->leftJoin('taxonomy_term_field_data', 'tf', 't.tid = tf.tid');
        $query->condition('tf.vid', 'technology', '=');
        $query->addExpression('COUNT(t.nid)', 'cnt' );
        $query->addExpression('tf.name', 'name' );
        $query->groupBy('name' );
        $resultsS = $query->execute()->fetchAll();
        $markup = '<div class="top"><ul>';
        foreach($resultsS as $term => $value)
        {
            $markup .='<li>';
            $markup .= '<a href="'.$value->name.'">'.$value->name.'<span>('.$value->cnt.')</span>'.'</a>';
            $markup .='</li>';
        }
        $markup .= '</ul></div>';

        return [
            '#markup' =>'<div>'.$markup.' '.$life.'</div>',
            '#cache'   => ['max-age' => 0]
        ];
    }
}