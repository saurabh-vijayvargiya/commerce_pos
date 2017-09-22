<?php

namespace Drupal\commerce_pos;

use Drupal\commerce_pos\Form\POSForm;
use Drupal\Core\Form\FormState;
use Drupal\commerce_order\Entity\Order;

/**
 * Provides main POS page.
 */
class POS {

  protected $store;

  /**
   * Builds the POS form.
   *
   * @return array
   *   A renderable array containing the POS form.
   */
  public function posForm() {
    $entity = $this->entityTypeManager->getStorage('entity_form_display')->load('commerce_product_variation.default.default');

    dpm($entity);

    //$entity->setComponent('yourfieldname', ['propertyyourechanging' => 'itsnewvalue']);

    $tempstore = \Drupal::service('user.private_tempstore')->get('commerce_pos');
    $register = $tempstore->get('register');

    if (empty($register) || !($register = \Drupal::entityTypeManager()->getStorage('commerce_pos_register')->load($register))) {
      return \Drupal::formBuilder()->getForm('\Drupal\commerce_pos\Form\RegisterSelectForm');
    }

    $store = $register->getStoreId();

    $order = Order::create([
      'type' => 'pos',
    ]);

    $order->setStoreId($store);

    $form_object = new POSForm(\Drupal::entityManager(), \Drupal::service('entity_type.bundle.info'), \Drupal::time(), \Drupal::currentUser());
    $form_object->setEntity($order);

    $form_object
      // ->setStringTranslation($this->stringTranslation)
      ->setModuleHandler(\Drupal::moduleHandler())
      ->setEntityTypeManager(\Drupal::entityTypeManager())
      ->setOperation('pos')
      ->setEntityManager(\Drupal::entityManager());

    $form_state = (new FormState())->setFormState([]);

    return \Drupal::formBuilder()->buildForm($form_object, $form_state);
  }

}
