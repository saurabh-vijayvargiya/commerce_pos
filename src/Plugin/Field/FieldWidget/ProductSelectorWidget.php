<?php

namespace Drupal\commerce_pos\Plugin\Field\FieldWidget;

use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'commerce_pos_product_selector' widget.
 *
 * @FieldWidget(
 *   id = "commerce_pos_product_selector",
 *   label = @Translation("Product Selector"),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   multiple_values = TRUE
 * )
 */
class ProductSelectorWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'number_of_results' => 10,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $formState) {
    $element = [];

    $element['number_of_results'] = [
      '#type' => 'textfield',
      '#title' => t("Number of results to show in selector dropdown"),
      '#default_value' => $this->getSetting('number_of_results'),
      '#access' => $this->fieldDefinition->isRequired(),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    $values = $items->getValue();

    $element += [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'commerce_product_variation',
      '#required' => $this->fieldDefinition->isRequired(),
      '#description' => t('Search by product title, start typing to begin your search.'),
      '#attributes' => [
        'class' => [
          'commerce-pos-product-autocomplete',
          'commerce-pos-product-search',
        ],
        'placeholder' => t('Product Search'),
      ],
    ];

    return ['target_id' => $element];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $order_item_storage = \Drupal::entityTypeManager()->getStorage('commerce_order_item');

    dpm($values);

    foreach ($values as $key => $value) {
      $product_variation = ProductVariation::load($value);
      $order_item = $order_item_storage->createFromPurchasableEntity($product_variation);
      $order_item->save();
      $values[$key] = $order_item->id();
    }

    dpm($values);

    return $values;
  }

}
