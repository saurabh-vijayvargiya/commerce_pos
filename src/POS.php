<?php

namespace Drupal\commerce_pos;

use Drupal\commerce_pos\Form\POSForm;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\commerce_order\Entity\Order;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\PrivateTempStoreFactory;

/**
 * Provides main POS page.
 */
class POS extends ControllerBase {

  /**
   * The container object.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * The tempstore object.
   *
   * @var \Drupal\user\SharedTempStore
   */
  protected $tempStore;

  /**
   * The currentOrder object.
   *
   * @var \Drupal\commerce_pos\CurrentOrder
   */
  protected $currentOrder;

  /**
   * Constructs a new POS object.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore factory.
   * @param \Drupal\commerce_pos\CurrentOrder $current_order
   *   The current order service.
   */
  public function __construct(ContainerInterface $container, PrivateTempStoreFactory $temp_store_factory, CurrentOrder $current_order) {
    $this->container = $container;
    $this->tempStore = $temp_store_factory->get('commerce_pos');
    $this->currentOrder = $current_order;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container,
      $container->get('user.private_tempstore'),
      $container->get('commerce_pos.current_order')
    );
  }

  /**
   * Builds the POS form.
   *
   * @param \Drupal\commerce_order\Entity\Order $commerce_order
   *   The order to edit.
   *
   * @return array
   *   A renderable array containing the POS form.
   */
  public function content(Order $commerce_order = NULL) {
    $register = \Drupal::service('commerce_pos.current_register')->get();

    if (empty($register) || !$register->isOpen()) {
      return $this->formBuilder()->getForm('\Drupal\commerce_pos\Form\RegisterSelectForm');
    }

    $store_id = $register->getStoreId();

    // If an order has been passed through, set that as the current order.
    if ($commerce_order) {
      $order = $commerce_order;
      $this->currentOrder->set($commerce_order);
    }
    // If we don't have an order, try to load the current order.
    else {
      $order = $this->currentOrder->get();
    }

    // If we still don't have an order.
    if (!$order) {
      $order = Order::create([
        'type' => 'pos',
        'store_id' => $store_id,
        'uid' => User::getAnonymousUser()->id(),
        'field_cashier' => $this->currentUser()->id(),
        'field_register' => $register->id(),
      ]);

      $order->save();

      $this->currentOrder->set($order);
    }

    $form_object = POSForm::create($this->container);
    $form_object->setEntity($order);

    $form_object
      ->setModuleHandler($this->moduleHandler())
      ->setEntityTypeManager($this->entityTypeManager())
      ->setOperation('pos')
      ->setEntityManager($this->entityManager());

    $form_state = (new FormState())->setFormState([]);

    // Save the existing order items in the order to the form state so we could
    // keep track of what changed during this particular transaction.
    $initial_items_on_order = [];
    foreach ($order->getItems() as $order_item) {
      /** @var \Drupal\commerce_order\Entity\OrderItem $order_item */
      $initial_items_on_order[$order_item->id()] = $order_item->getPurchasedEntityId();
    }
    $form_state->set('initial_items_on_order', $initial_items_on_order);

    // Set the step to edit order, if we're editing a completed order.
    if ($commerce_order || $order->getState()->getValue()['value'] == 'completed') {
      $form_state->set('is_edit_order', TRUE);
    }

    return $this->formBuilder()->buildForm($form_object, $form_state);
  }

}
