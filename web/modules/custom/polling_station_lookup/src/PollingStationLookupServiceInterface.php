<?php

namespace Drupal\polling_station_lookup;

/**
 * Interface PollingStationLookupServiceInterface.
 */
interface PollingStationLookupServiceInterface {

  /**
   * Lookup polling station(s) by a catchment postcode.
   */
  public function lookupByPostcode($postcode);

  /**
   * Lookup polling stations by identifier.
   *
   * Lookup polling station(s) by "slug" from address-picker list returned by
   * ambiguous postcode search.
   */
  public function lookupByAddress($slug);

}
