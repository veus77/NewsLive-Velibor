<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/7/17
 * Time: 11:20 AM
 */

namespace Drupal\register_popup_form\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class RegisterPopupForm extends FormBase
{
    public function getFormId()
    {
        return 'register_popup_form';

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#size' => 60,
            '#maxlength' => 30,
            '#required' => TRUE,
        );

        $form['username'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Username'),
            '#size' => 60,
            '#maxlength' => 30,
            '#required' => TRUE,
        );


        $form['passc'] = array(
            '#type' => 'password_confirm',
//            '#title' => $this->t('Verify password'),
            '#size' => 25,
            '#required' => TRUE,
        );

        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#required' => TRUE,
        );


        $form['emailc'] = [
            '#type' => 'email',
            '#title' => $this->t('Verify email'),
            '#value' => empty($form['#value']) ? NULL : $form['#value']['email-conf'],
            '#attributes' => ['class' => ['email-confirm', 'js-email-confirm']],
            '#error_no_message' => TRUE,
            '#required' => TRUE,
        ];


        $form['register'] = array(
            '#type' => 'submit',
            '#value' => t('Register '),
        );

        $form['captcha'] = array(
            "#type" => "captcha",
            "#captcha_type" => "image_captcha/Image"
        );

        $form[ '#theme' ] = 'register_popup_form';


        return $form;
        $bool=true;
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
//        $email =['email']['#value'];
//        $email2 =['email-conf']['#value'];
//        if (strlen($email) > 0 || strlen($email2) > 0) {
//            if (strcmp($email, $email2)) {
//                $form_state->setError($form, t('The specified emails do not match.'));
//            }
//        } elseif (['#required'] && $form_state->getUserInput()) {
//            $form_state->setError($form, t('Email field is required.'));
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

}