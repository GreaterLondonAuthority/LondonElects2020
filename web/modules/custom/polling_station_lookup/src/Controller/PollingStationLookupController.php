<?php

namespace Drupal\polling_station_lookup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Class PollingStationLookupController.
 */
class PollingStationLookupController extends ControllerBase {

  use StringTranslationTrait;

  const ROUTE_BASE = "/polling-stations";
  const MSG_NO_STATION_FOUND = "No polling stations can be found";
  const TITLE = "Polling Station details for @postcode";


  /**
   * Postcode lookup.
   *
   * Return polling station data.
   */
  public function postcodeLookup($postcode) {
    try {
      if (!$postcode) {
        $postcode = \Drupal::request()->query->get('postcode-search');
      }
      $response = \Drupal::service('polling_station_lookup')
        ->lookupByPostcode($postcode);
      return $this->formatResponse($response, $postcode);
    } catch (PollingStationLookupException $e) {
      return $this->redirect($this->getLookupRoute());
    }
  }

  /**
   * Address lookup.
   *
   * Return polling station data.
   */
  public function addressLookup($postcode, $slug) {
    try {
      $response = \Drupal::service('polling_station_lookup')
        ->lookupByAddress($slug);
      return $this->formatResponse($response, $postcode);
    } catch (PollingStationLookupException $e) {
      return $this->redirect($this->getLookupRoute());
    }
  }

  /**
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public function getTitle($postcode) {
    return $this->t($this::TITLE, ['@postcode' => $postcode]);
  }

  /**
   * Format the response data as a Drupal render array
   */
  private function formatResponse($response, $postcode) {
    $response_data = \Drupal::service('polling_station.response_parser')->parsePollingStationLookupResponse($response);

    if ($response_data['address-picker']) {
      return $this->formatAddressList($response_data, $postcode);
    }

    return $this->formatPollingStation($response_data, $postcode);
  }

  /**
   * Format address picker list as links
   */
  private function formatAddressList($response_data, $postcode) {
    $render_array['postcode'] = ['#markup' => $postcode];

    $links = [];
    foreach ($response_data['addresses'] as $address) {
      $links[] = [
        'title' => $address['address'],
        'url' => Url::fromUri('internal:' . self::ROUTE_BASE . '/address/' . $postcode . '/' . $address['slug']),
        'attributes' => [
          'class' => [
            'links__link',
          ],
        ],
      ];
    }

    $render_array['links'] = [
      '#theme' => 'links',
      '#attributes' => [
        'class' => [
          'links',
        ],
      ],
      '#links' => $links,
    ];

    $content = [
      '#theme' => 'polling_station_lookup_address_list',
      '#content' => $render_array,
      '#return_path' => $this->getLookupRoute(),
    ];

    return $content;
  }

  /**
   * Format Polling stations returned by response parser for theme function
   *
   * @param $search_term
   * @param $response_data
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  private function formatPollingStation($response_data, $postcode) {
    $station_address = [];
    $render_array = [];
    $render_array['message'] = ['#markup' => $this->t("@message", ["@message" => self::MSG_NO_STATION_FOUND])];
    $render_array['postcode'] = ['#markup' => $postcode];

    if (!empty($response_data['stations'])) {
      $station = $this->getSinglePollingStation($response_data['stations']);

      if ($station && $station['polling_station_known']) {
        $station_address = array_merge($station['address'], [$station['postcode']]);
        $render_array['message'] = NULL;
        $render_array['address_line_1'] = [
          '#markup' => reset($station_address),
        ];
        $render_array['address'] = [
          '#markup' => implode('<br/>', $station_address),
        ];
        $render_array['coordinates'] = [
          '#theme' => 'item_list',
          '#items' => $station['coordinates'],
        ];
      }
    }

    if (isset($response_data['electoral_services'])) {
      $electoral_services = $response_data['electoral_services'];
      $render_array['electoral_services'] = [
        'name' => [
          '#markup' => $electoral_services['name'],
        ],
        'address' => [
          '#markup' => str_replace(PHP_EOL, '<br/>', $electoral_services['address']) . '<br/>' . $electoral_services['postcode'],
        ],
        'contacts' => [
          'email' => ['#markup' => $electoral_services['email']],
          'phone' => ['#markup' => $electoral_services['phone']],
          'website' => ['#markup' => $electoral_services['website']],
        ],
      ];
    }

    $content = [
      '#theme' => 'polling_station_lookup',
      '#content' => $render_array,
      '#return_path' => $this->getLookupRoute(),
      '#gmap_src' => \Drupal::service('polling_station.address_parser')->getGoogleMapSrcFromAddress($station_address),
    ];

    return $content;
  }

  /**
   * The response parser will return multiple stations if there are several
   * upcoming ballots in the postcode area.
   */
  private function getSinglePollingStation($stations) {
    if (empty($stations)) {
      return NULL;
    }

    if ($ballot_date = getenv('DC_BALLOT_DATE')) {
      return $this->getPollingStationByBallotDate($stations, $ballot_date);
    }
    else {
      return $this->getFirstPollingStation($stations);
    }
  }

  /**
   * Sort available stations by ballot date and return the next upcoming one.
   */
  private function getFirstPollingStation($stations) {
    usort($stations, function ($item1, $item2) {
      return $item1['date'] <=> $item2['date'];
    });
    return reset($stations);
  }

  /**
   * Return the station with the specified ballot date.
   */
  private function getPollingStationByBallotDate($stations, $ballot_date) {
    foreach ($stations as $station) {
      if ($station['date'] == $ballot_date) {
        return $station;
        break;
      }
    }

    return NULL;
  }

  /**
   * Get a route to a node containing a polling station search slice.
   *
   * In the case of multiple results this function returns the first one found.
   * Although it is possible to have multiple nodes containing this type of
   * slice, sensibly there should only be one.
   *
   * @return \Drupal\Core\GeneratedUrl|string|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  private function getLookupRoute() {
    $pids = \Drupal::entityQuery('paragraph')
      ->condition('type', 'slice_polling_station_search')
      ->execute();
    if (empty($pids)) {
      return NULL;
    }

    $nids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('field_slices_pre.target_id', $pids, 'IN')
      ->execute();
    if (empty($nids)) {
      return NULL;
    }

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $node_storage->load(reset($nids));
    if ($node) {
      return $node->toUrl()->toString();
    }

    return NULL;
  }

}
