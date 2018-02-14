<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/7/17
 * Time: 9:42 AM
 */

namespace Drupal\login_popup_form\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class LoginPopupForm extends FormBase
{
    public function getFormId()
    {
        return 'login_popup_form';

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['username'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Username'),
            '#size' => 60,
            '#maxlength' => 30,
            '#required' => TRUE,
        );


        $form['pass'] = array(
            '#type' => 'password',
            '#title' => $this->t('Password'),
            '#size' => 25,
            '#required' => TRUE,
        );

        $form['remember'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Remember Me'),
        );

        $form['login'] = array(
            '#type' => 'submit',
            '#value' => t('log in '),
        );

        $form[ '#theme' ] = 'login_popup_form';

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

}