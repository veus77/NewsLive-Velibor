<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/24/17
 * Time: 4:09 PM
 */

namespace Drupal\block_news\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "blocknews_block",
 *   admin_label = @Translation("Pager"),
 *   category = @Translation("Prewiew"),
 * )
 */
class BlockNewsBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $current_id = intval(\Drupal::routeMatch()->getParameter('node')->Id());

        $query =  \Drupal::database()->select('node', 'n');
        $query->condition('n.nid', $current_id,'>');
        $query->condition('n.type', 'news', '=');
        $query->range(0,1);
        $query->addField('n', 'nid');
        $next_id = $query->execute()->fetchField();


        $query =  \Drupal::database()->select('node', 'n');
        $query->condition('n.nid', $current_id,'<');
        $query->condition('n.type', 'news','=');
        $query->range(0,1);
        $query->orderBy('nid','DESC');
        $query->addField('n', 'nid');
        $prev_id = $query->execute()->fetchField();


        $markup = '<div>';
        if( isset($prev_id) && is_numeric($prev_id)) {
            $markup .= '<a class="prev" href="/node/'.$prev_id.'">Prev</a>';
        }
        if( isset($next_id)&& is_numeric($next_id)) {
            $markup .= '<a class="next" href="/node/'.$next_id.'">Next</a>';
        }
        $markup .= '</div>';


        return [
            '#markup' =>$markup,
            '#cache'   => ['max-age' => 0]
        ];
    }
}