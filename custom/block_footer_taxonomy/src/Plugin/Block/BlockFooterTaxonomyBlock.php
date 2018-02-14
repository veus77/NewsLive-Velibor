<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/28/17
 * Time: 9:33 AM
 */

namespace Drupal\block_footer_taxonomy\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "blockFooter taxonomy",
 *   admin_label = @Translation("Tags"),
 *   category = @Translation("Other"),
 * )
 */
class BlockFooterTaxonomyBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $query = \Drupal::database()->select('taxonomy_index','t' );
        $query->leftJoin('taxonomy_term_field_data', 'tf', 't.tid = tf.tid');
        $query->addExpression('COUNT(t.nid)', 'cnt' );
        $query->addExpression('tf.name', 'name' );
        $query->groupBy('name' );
        $results = $query->execute()->fetchAll();
        $markup = '<div><ul>';
                foreach($results as $term => $value)
                {
                    $markup .='<li>';
                        $markup .= '<a href="'.$value->name.'">'.$value->name.'<span>'.$value->cnt.'</span>'.'</a>';
                    $markup .='</li>';
                }
        $markup .= '</ul></div>';

        $bool = true;
        return [
            '#markup' =>$markup,
            '#cache'   => ['max-age' => 0]
        ];
    }

}