<?php

namespace Drupal\Tests\sensible_email_validation\Unit;

use Drupal\sensible_email_validation\SensibleEmailValidator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\sensible_email_validation\SensibleEmailValidator
 * @group sensible_email_validation
 */
class SensibleEmailValidatorTest extends UnitTestCase {

  /**
   * @var SensibleEmailValidator
   */
  private $validator;

  public function setUp() {
    $this->validator = new SensibleEmailValidator();
  }

  /**
   * Test that SensibleEmailValidator doesn't interfere with normal valid addresses
   */
  public function testAddressWithTld() {
    $this->assertTrue($this->validator->isValid('test100@numiko.net'));
  }

  public function testAddressWithoutTld() {
    $this->assertFalse($this->validator->isValid('test100@numiko'));
  }

  /**
   * Test that SensibleEmailValidator passes failure result straight back from
   * default validator class.
   */
  public function testEmptyAddress() {
    $this->assertFalse($this->validator->isValid(''));
  }

}
