<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/7/17
 * Time: 9:46 AM
 */

namespace Drupal\login_popup_form\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "login_popup_block",
 *   admin_label = @Translation("Login Popup Block"),
 *   category = @Translation("Other"),
 * )
 */

class LoginPopupBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $form = \Drupal::formBuilder()->getForm('Drupal\login_popup_form\Form\LoginPopupForm');
        return $form;

    }
}