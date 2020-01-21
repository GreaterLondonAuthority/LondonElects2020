<?php

/**
 * @file
 * Contains factory for creating jump link items from slice.
 */

namespace Drupal\slice_jump_links\Factory;

use Drupal\slice_jump_links\Entity\JumpLink;
use Drupal\slice_jump_links\Entity\JumpLinkButton;
use Drupal\slice_jump_links\Exception\JumpLinkException;
use Drupal\Core\Entity\EntityInterface;

abstract class AbstractEntityJumpLinkFactory {

  const JUMP_LINK_FIELD = 'field_jump_link_label';
  const CTA_FIELD = 'field_jump_links_cta';
  const ANCHOR_FIELD = 'field_anchor';

  const CACHING_ENABLED = true;

  abstract public function fetch(EntityInterface $entity);

  /**
   * Fetch a jump link or jump link button
   */
  public function fetchLinkOfType($jumpLinkType, EntityInterface $entity) {

    $cache_key = ['slice_jump_links', $jumpLinkType, 'entity'];
    $cache_key[] = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $cache_key[] = $entity->getType();
    $cache_key[] = $entity->id();
    if ($entity->hasField('changed')) {
      $cache_key[] = $entity->get('changed')->first()->getValue()['value'];
    } else {
      // For paragraphs we take the parent entity to get the changed date.
      $parent = $entity->getParentEntity();
      if ($parent) {
        if ($parent->hasField('changed')) {
          $cache_key[] = $parent->get('changed')->first()->getValue()['value'];
        }
      }
    }
    $cid = implode(':', $cache_key);

    $data = NULL;
    if (static::CACHING_ENABLED && $cache = \Drupal::cache()->get($cid)) {
      return $cache->data;
    }
    else {
      $data = $this->create($entity);
      \Drupal::cache()->set($cid, $data);
    }

    return $data;

  }

}