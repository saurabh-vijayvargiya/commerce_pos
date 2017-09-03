<?php

namespace Drupal\commerce_pos;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

class Registers {

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\EntityQuery
   */
  protected $entity_query;

  /**
   * Construct a new CashierUsers Object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   Pass in the connection via dependency injection, standard for fields.
   */
  public function __construct() {
    $this->entity_query = QueryFactory::create();
  }

  /**
   * Get the list of users having the 'pos_cashier' role.
   *
   * @return array
   *   An array of User Ids having 'pos_cashier' role.
   */
  public function getRegisters() {
    $query = $this->entity_query->get('commerce_pos_register')
      ->condition('status', 1);

    $ids = $query->execute();

    $registers = \Drupal::entityTypeManager()->getStorage('commerce_pos_register')->loadMultiple($ids);

    return $registers;
  }
}