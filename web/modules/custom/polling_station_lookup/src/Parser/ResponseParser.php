<?php


namespace Drupal\polling_station_lookup\Parser;

/**
 * Class ResponseParser.
 *
 * @package Drupal\polling_station_lookup
 */
class ResponseParser {

  /**
   * Parses lookup response JSON data to an array of variables
   */
  public function parsePollingStationLookupResponse($response) {
    $parsed_data = [
      'address-picker' => FALSE,
      'addresses' => [],
      'stations' => []
    ];

    if (!$response) {
      return $parsed_data;
    }

    $parsed_data['address-picker'] = $response->address_picker;
    $parsed_data['electoral_services'] = (array) $response->electoral_services;

    if ($response->address_picker) {
      foreach ($response->addresses as $address) {
        $parsed_data['addresses'][$address->address] = (array) $address;
      }
      return $parsed_data;
    }

    if (empty($response->dates)) {
      return $parsed_data;
    }

    foreach ($response->dates as $date) {
      $station_data = [
        'date' => $date->date,
        'polling_station_known' => $date->polling_station->polling_station_known,
        'custom_finder' => $date->polling_station->custom_finder
      ];
      if ($date->polling_station->polling_station_known) {
        $station = $date->polling_station->station;
        $address = $station->properties->address;
        $station_data['address'] = explode(PHP_EOL, $address);
        $station_data['postcode'] = $station->properties->postcode;
        $station_data['coordinates'] = $station->geometry->coordinates;
      }
      $parsed_data['stations'][] = $station_data;
      }

    return $parsed_data;
  }

}
