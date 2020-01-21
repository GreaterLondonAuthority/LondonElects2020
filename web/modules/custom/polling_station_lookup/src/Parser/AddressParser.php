<?php

namespace Drupal\polling_station_lookup\Parser;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Component\Utility\Html;

/**
 * Class PollingStationLookupService.
 */
class AddressParser {

  private $key;

  /**
   * Constructs a new PollingStationLookupService object.
   */
  public function __construct(ConfigFactory $config_factory) {
    $settings = $config_factory->get('polling_station_lookup.config');
    $this->key = $settings->get('gmaps_api.key');
  }

  /**
   * Format address as a Google Maps source string.
   */
  public function getGoogleMapSrcFromAddress(array $address = []) {
    if (empty($address) || !$this->key) {
      return NULL;
    }

    $escapedAddress = [];
    foreach ($address as $item) {
      $escapedAddress[] = Html::escape($item);
    }
    $q = implode(', ', $escapedAddress);

    $src = "https://www.google.com/maps/embed/v1/place?key={$this->key}&q=$q";

    return $src;
  }

}
