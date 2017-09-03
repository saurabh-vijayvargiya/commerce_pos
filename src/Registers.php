<?php

namespace Drupal\commerce_pos;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

class Registers {

  /**
   * Get the list of users having the 'pos_cashier' role.
   *
   * @return array
   *   An array of User Ids having 'pos_cashier' role.
   */
  public function getRegisters() {
    $query = \Drupal::entityQuery('commerce_pos_register');

    $ids = $query->execute();

    $registers = \Drupal::entityTypeManager()->getStorage('commerce_pos_register')->loadMultiple($ids);

    return $registers;
  }
}