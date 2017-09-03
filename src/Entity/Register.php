<?php

namespace Drupal\commerce_pos\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Register entity.
 *
 * @ContentEntityType(
 *   id = "commerce_pos_register",
 *   label = @Translation("Register"),
 *   label_singular = @Translation("register"),
 *   label_plural = @Translation("registers"),
 *   label_count = @PluralTranslation(
 *     singular = "@count register",
 *     plural = "@count registers",
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_pos\RegisterListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_pos\Form\RegisterForm",
 *       "edit" = "Drupal\commerce_pos\Form\RegisterForm",
 *       "delete" = "Drupal\commerce_pos\Form\RegisterDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "commerce_pos_register",
 *   data_table = "commerce_pos_register_field_data",
 *   admin_permission = "access commerce pos administration pages",
 *   entity_keys = {
 *     "id" = "register_id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/pos/register/{commerce_pos_register}",
 *     "add-form" = "/admin/commerce/pos/register/add",
 *     "edit-form" = "/admin/commerce/pos/register/{commerce_pos_register}/edit",
 *     "delete-form" = "/admin/commerce/pos/register/{commerce_pos_register}/delete",
 *     "collection" = "/admin/commerce/pos/registers"
 *   }
 * )
 */
class Register extends ContentEntityBase implements RegisterInterface {

  /**
   * The Register ID.
   *
   * @var string
   */
  protected $register_id;

  /**
   * The Register label.
   *
   * @var string
   */
  protected $label;

  /**
   * {@inheritdoc}
   */
  public function getStore() {
    return $this->get('store_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setStore(StoreInterface $store) {
    $this->set('store_id', $store->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStoreId() {
    return $this->get('store_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setStoreId($store_id) {
    $this->set('store_id', $store_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['store_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Store'))
      ->setDescription(t('The store where the register is located.'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setSetting('target_type', 'commerce_store')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Some sort of currency or price field maybe?
    $fields['cash'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('Cash'))
      ->setDescription(t('The value of all the cash in the register.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'commerce_price_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'commerce_price_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
