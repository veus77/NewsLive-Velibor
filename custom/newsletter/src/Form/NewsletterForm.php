<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/24/17
 * Time: 9:54 AM
 */

namespace Drupal\newsletter\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

class NewsletterForm extends FormBase
{
    public function getFormId()
    {
        return 'newsletter_form_block';

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['details'] = [
            '#markup' => '<h3>Get Updates</h3>',
        ];
        $form['subtitle'] = [
            '#markup' => '<p class="subtittle">Subscribe our newsletter to get the best stories into your inbox!</p>',
        ];
        $form['email'] = array(
            '#type' => 'email',
            '#placeholder' => 'E-mail',
            '#size' => 32,
            '#required' => false,
            '#ajax' => [
                'callback' => array($this, 'ajaxFormSubmit'),
                'event' => 'change',
                'progress' => array(
                    'type' => 'throbber',
                    'message' => t('Verifying email...'),
                ),
            ],
        );

        $form['actions'] = array(
            '#type' => 'submit',
            '#value' => t('Subscribe'),
        );
        $form['message'] = [
            '#type' => 'container',
            '#attributes' => [
                'id' => 'newsletter-message',
            ],
        ];

        $form['description'] = array(
            '#markup' => '<p class="spam">Dont\'t worry we hate spams</p>',
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */

    protected function validateEmail(array &$form, FormStateInterface $form_state) {
        if (substr($form_state->getValue('email'), -4) !== '.com') {
            return FALSE;
        }
        return TRUE;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (!$this->validateEmail($form, $form_state)) {
            $form_state->setErrorByName('email', $this->t('This is not a .com email address.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

    public function ajaxFormSubmit(array $form, FormStateInterface $form_state)
    {
        $values = $form_state->getValue('email');
        $valid = $this->validateEmail($form, $form_state);
        $response = new AjaxResponse();

        $email = \Drupal::database()->select('newsletter', 'n');
        $email->condition('n.email', $values, '=');
        $email->addField('n', 'email');
        $emails = $email->execute()->fetchAll();

            if ($valid && empty($emails)) {

                $insert = \Drupal::database()->insert('newsletter');
                $insert->fields([
                    'email',
                    'date',
                ]);
                $insert->values([
                    $values,
                    date('F d, Y'),
                ]);

                $insert->execute();

                $message = $this->t('<p class="success">Thank you for your trust.</p>');
            } else {


                $message = $this->t('<p class="fail">Email not valid or exist in database. Please type correct address!</p>');
            }
            $response->addCommand(new HtmlCommand('#newsletter-message', $message));
            return $response;
        }
}
