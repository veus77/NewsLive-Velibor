<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/7/17
 * Time: 11:23 AM
 */

namespace Drupal\register_popup_form\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "register_popup_block",
 *   admin_label = @Translation("Register Popup Block"),
 *   category = @Translation("Other"),
 * )
 */

class RegisterPopupBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $form = \Drupal::formBuilder()->getForm('Drupal\register_popup_form\Form\RegisterPopupForm');
        return $form;

    }
}