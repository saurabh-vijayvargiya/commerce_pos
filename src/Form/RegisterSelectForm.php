<?php

namespace Drupal\commerce_pos\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views_ui\ViewFormBase;

/**
 * Show a form to select the current register for this session.
 */
class RegisterSelectForm extends Form implements FormInterface {

  public function getFormId() {
    return 'commerce_pos_register_select';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $registers = \Drupal::service('commerce_pos.registers');

    dpm($registers);

    if (empty($registers)) {
      //Return no registers error, link to setup registers.
    }

    $register_options = [];
    foreach ($registers as $id => $register) {
      $register_options[$id] = $register->title;
    }

    $form['register'] = array(
      '#type' => 'select',
      '#title' => 'Select Register',
      '#options' => $register_options,
    );

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tempstore = \Drupal::service('user.private_tempstore')->get('commerce_pos');
    $tempstore->set('register', $form_state->getValue('register'));
  }

}
