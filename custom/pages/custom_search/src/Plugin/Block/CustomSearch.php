<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/18/17
 * Time: 9:18 AM
 */

namespace Drupal\custom_search\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "search_custom_block",
 *   admin_label = @Translation("Custom Search"),
 *   category = @Translation("other"),
 * )
 */

class CustomSearch extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $form = \Drupal::formBuilder()->getForm('Drupal\custom_search\Form\CustomSearch');
        return $form;

    }
}