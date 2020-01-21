<?php

/**
 * @file
 * Contains factory for creating jump link list from node with slices.
 */

namespace Drupal\slice_jump_links\Factory;

use Drupal\slice_jump_links\Factory\EntityJumpLinkFactory;
use Drupal\slice_jump_links\Exception\JumpLinkException;
use Drupal\slice_jump_links\ItemList\JumpLinkItemList;
use Drupal\node\Entity\Node;

class NodeJumpLinkListFactory {

  const CACHING_ENABLED = true;

  const SLICE_ENTITY_TYPE = 'paragraph';

  /**
   * Fetch the jump link list
   */
  public function fetch(Node $node) {

    \Drupal\Component\Utility\Timer::start('node_jump_links');

    $cache_key = ['slice_jump_links', 'list'];
    $cache_key[] = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $cache_key[] = $node->id();
    $cache_key[] = $node->get('changed')->first()->getValue()['value'];
    $cid = implode(':', $cache_key);

    $data = NULL;
    if (static::CACHING_ENABLED && $cache = \Drupal::cache()->get($cid)) {
      $data = $cache->data;
    }
    else {
      $data = $this->create($node);
      \Drupal::cache()->set($cid, $data);
    }

    return $data;
 
  }

  /**
   * Create a jump link list from an entity with a slices field.
   */
  public function create(Node $node) {

    $jumpLinkList = new JumpLinkItemList();

    // Create the initial node jump link
    try {
      $jumpLink = \Drupal::Service('jump_link.factory.entity')->fetchOnlyLink($node);
      $jumpLinkList->addItem($jumpLink);
    } catch (JumpLinkException $e) {
      // This is a non-jump linkable slice
    }

    // Create a jump link for each of the slices
    $slices = [];
    foreach ($node->getFieldDefinitions() as $field_name => $field) {
      $fieldStorage = $field->getFieldStorageDefinition();
      if ($fieldStorage->getType() == 'entity_reference_revisions' && $fieldStorage->getSetting('target_type') == self::SLICE_ENTITY_TYPE) {
        foreach ($node->get($field_name) as $slice) {
          $slices[] = $slice->getValue()['target_id'];
        }
      }
    }

    if ($slices) {
      foreach (\Drupal::entityManager()->getStorage(self::SLICE_ENTITY_TYPE)->loadMultiple($slices) as $slice) {
        try {
          $jumpLink = \Drupal::Service('jump_link.factory.entity')->fetchOnlyLink($slice);
          $jumpLinkList->addItem($jumpLink);
        } catch (JumpLinkException $e) {
          // This is a non-jump linkable slice
        }
      }
    }

    // Create the CTA button
    try {
      $jumpLinkButton = \Drupal::Service('jump_link_button.factory.entity')->fetch($node);
      $jumpLinkList->addButton($jumpLinkButton);
    } catch (JumpLinkException $e) {
      // This is a non-jump linkable slice
    }

    return $jumpLinkList;

  }

}