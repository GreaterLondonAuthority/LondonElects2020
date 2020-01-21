<?php

/**
 * @file
 * Contains factory for creating jump link items from slice.
 */

namespace Drupal\slice_jump_links\Factory;

use Drupal\slice_jump_links\Factory\AbstractEntityJumpLinkFactory;
use Drupal\slice_jump_links\Entity\JumpLink;
use Drupal\slice_jump_links\Entity\JumpLinkButton;
use Drupal\slice_jump_links\Exception\JumpLinkException;
use Drupal\Core\Entity\EntityInterface;

class EntityJumpLinkFactory extends AbstractEntityJumpLinkFactory {

  /**
   * Fetch a jump link & verify it is a jump link
   */
  public function fetchOnlyLink(EntityInterface $entity) {
    $jumpLink = $this->fetch($entity);
    if ($jumpLink->justAnchor()) {
      throw new JumpLinkException('Not enough information to create a jump link');
    }
    return $jumpLink;
  }

  /**
   * Fetch a jump link
   */
  public function fetch(EntityInterface $entity) {
    return parent::fetchLinkOfType('jump_link', $entity);
  }

  /**
   * Create a jump link item from an entity with a jump link field
   */
  public function create(EntityInterface $entity) {

    $jumpLink = new JumpLink();

    if ($entity->hasField(static::JUMP_LINK_FIELD) && !$entity->get(static::JUMP_LINK_FIELD)->isEmpty()) {
      $jumpLink->setLabel($entity->get(static::JUMP_LINK_FIELD)->first()->getString());
    }

    if ($entity->hasField(static::ANCHOR_FIELD) && !$entity->get(static::ANCHOR_FIELD)->isEmpty()) {
      $jumpLink->setAnchor($entity->get(static::ANCHOR_FIELD)->first()->getString());
    }

    if ($jumpLink->isEmpty()) {
      throw new JumpLinkException('Not enough information to create a jump link');
    }

    return $jumpLink;

  }

}