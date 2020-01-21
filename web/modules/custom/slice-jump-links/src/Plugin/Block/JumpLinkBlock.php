<?php

namespace Drupal\slice_jump_links\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\slice_jump_links\Factory\NodeJumpLinkListFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block for displaying jump links
 *
 * @Block(
 *   id = "jump_link_block",
 *   admin_label = @Translation("Jump link block"),
 * )
 */
class JumpLinkBlock extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {

  /**
   * @var RouteMatchInterface
   */
  private $currentRouteMatch;

  /**
   * @var NodeJumpLinkListFactory
   */
  private $nodeJumpLinkListFactory;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RouteMatchInterface $currentRouteMatch,
    NodeJumpLinkListFactory $nodeJumpLinkListFactory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->nodeJumpLinkListFactory = $nodeJumpLinkListFactory;
  }

  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('jump_link.list.factory.node')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');
    if ($node) {
      $jumpLinkList = $this->nodeJumpLinkListFactory->fetch($node);
      if (!$jumpLinkList->isEmpty()) {
        return [
          '#theme' => 'slice_jump_links',
          '#list' => $jumpLinkList
        ];
      }
    }
    return [];
  }

  /**
   * Cache the block per URL.
   *
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url'];
  }

  /**
   * Invalidate the cache when any node changes.
   *
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $node = $this->currentRouteMatch->getParameter('node');
    if ($node) {
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    } else {
      return parent::getCacheTags();
    }
  }
}
