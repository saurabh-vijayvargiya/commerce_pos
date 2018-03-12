<?php

namespace Drupal\Tests\commerce_pos\FunctionalJavascript;

use Drupal\commerce_pos\Entity\Register;
use Drupal\commerce_price\Price;
use Drupal\FunctionalJavascriptTests\JavascriptTestBase;
use Drupal\Tests\commerce_pos\Functional\CommercePosCreateStoreTrait;

/**
 * Tests the Register selection.
 *
 * @group commerce_pos
 */
class SelectRegisterTest extends JavascriptTestBase {
  use CommercePosCreateStoreTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_pos',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $test_store = $this->setUpStore();
    $register = Register::create([
      'store_id' => $test_store->id(),
      'name' => 'Other register',
      'default_float' => new Price('100.00', 'USD'),
    ]);
    $register->save();
    $this->drupalLogin($this->rootUser);
  }

  /**
   * Tests adding and removing products from the POS form.
   */
  public function testRegisterSelection() {
    $web_assert = $this->assertSession();
    $this->drupalGet('admin/commerce/pos/main');

    $this->getSession()->getPage()->fillField('register', '1');
    $this->getSession()->getPage()->fillField('float[number]', '10.00');
    $this->getSession()->getPage()->findButton('Open Register')->click();
    $current_register_name = Register::load($this->getSession()->getCookie('commerce_pos_register'))->getName();
    // Goto register seletion page.
    $this->drupalGet('admin/commerce/pos/register');

    // Asserting current register.
    $field = $this->assertSession()->optionExists('register', 1)->getText();
    $this->assertEquals($field, $current_register_name);
    $this->getSession()->getPage()->fillField('register', '2');
    $this->getSession()->getPage()->fillField('float[number]', '10.00');
    $this->getSession()->getPage()->findButton('Open Register')->click();

    $this->drupalGet('admin/commerce/pos/main');
    $web_assert->pageTextNotContains("Register: $current_register_name");
    $current_register_name = Register::load($this->getSession()->getCookie('commerce_pos_register'))->getName();
    $web_assert->pageTextContains("Register: $current_register_name");
  }

}
