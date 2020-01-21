<?php

namespace Drupal\polling_station_lookup\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the polling_station_lookup module.
 */
class PostcodeLookupControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "polling_station_lookup PollingStationLookupController's controller functionality",
      'description' => 'Test Unit for module polling_station_lookup and controller PollingStationLookupController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests polling_station_lookup functionality.
   */
  public function testPostcodeLookupController() {
    // Check that the basic functions of module polling_station_lookup.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
