<?php

namespace Drupal\polling_station_lookup;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class PollingStationLookupService.
 */
class PollingStationLookupService implements PollingStationLookupServiceInterface {

  private $client;
  private $dc_api_key;
  private $dc_base_url;

  /**
   * Constructs a new PollingStationLookupService object.
   */
  public function __construct(ConfigFactory $config_factory) {
    $settings = $config_factory->get('polling_station_lookup.config');
    $this->dc_api_key = $settings->get('dc_api.key');
    $this->dc_base_url = $settings->get('dc_api.url');
    $config = [
      'headers' => [
        'Authorization' => 'Token ' . $this->dc_api_key
      ]
    ];
    $this->client = new HttpClient($config);
  }

  /**
   * {@inheritDoc}
   */
  public function lookupByPostcode($postcode) {
    return $this->makeRequest('/postcode/' . $postcode);
  }

  /**
   * {@inheritDoc}
   */
  public function lookupByAddress($slug) {
    return $this->makeRequest('/address/' . $slug);
  }

  /**
   * Check that the module has been configured with Democracy Club values.
   *
   * @return bool
   */
  public function isConfigured() {
    if (!$this->dc_base_url || !$this->dc_api_key) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Query the client with the given url.
   *
   * Append the url suffix to the base url and query using the
   * guzzle http client.
   */
  protected function makeRequest($urlSuffix) {
    // Do not attempt request if module is not configured.
    if (!$this->isConfigured()) {
      return NULL;
    }

    try {
      $response = $this->client->get($this->dc_base_url . $urlSuffix);
      $body = $response->getBody();
     return json_decode($body);
     } catch (ClientException $e) {
      return NULL;
    }
  }
}
