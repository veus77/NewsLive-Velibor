<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/24/17
 * Time: 9:53 AM
 */
/**
 * @file
 * Contains \Drupal\newsletter\Plugin\Block\NewsletterBlock.
 */
namespace Drupal\newsletter\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;



/**
 * Provides a custom block
 *
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("Newsletter"),
 *   category = @Translation("Subscribe"),
 * )
 */
class NewsletterBlock extends BlockBase
{
    public function build()
    {
        // TODO: Implement build() method.
        $form = \Drupal::formBuilder()->getForm('Drupal\newsletter\Form\NewsletterForm');
        return $form;
    }
}