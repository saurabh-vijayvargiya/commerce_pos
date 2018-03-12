<?php

namespace Drupal\commerce_pos\Plugin\Derivative;

use Drupal\commerce_pos\Registers;
use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines dynamic local tasks for select register.
 */
class DynamicSelectRegister extends DeriverBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $registers = new Registers();
    $registers = $registers->getRegisters();
    if (count($registers) > 1) {
      $this->derivatives['select_register'] = $base_plugin_definition;
      $this->derivatives['select_register']['title'] = $this->t("Select Register");
      $this->derivatives['select_register']['base_route'] = 'commerce_pos.main';
      $this->derivatives['select_register']['route_name'] = 'commerce_pos.select_register';
      return $this->derivatives;
    }
    return '';
  }

}

