<?php

namespace Drupal\commerce_pos;

use Drupal\commerce_pos\Form\POSForm;
use Drupal\Core\Form\FormState;
use Drupal\commerce_order\Entity\Order;

/**
 * Provides main POS page.
 */
class POS {

  /**
   * Builds the POS form.
   *
   * @return array
   *   A renderable array containing the POS form.
   */
  public function posForm() {
    $order = Order::create([
      'type' => 'pos',
    ]);

    dpm($order);

    $form_object = new POSForm(\Drupal::entityManager(), \Drupal::service('entity_type.bundle.info'), \Drupal::time(), \Drupal::currentUser());
    $form_object->setEntity($order);

    $form_state = (new FormState())->setFormState([]);

    return \Drupal::formBuilder()->buildForm($form_object, $form_state);
  }

}
