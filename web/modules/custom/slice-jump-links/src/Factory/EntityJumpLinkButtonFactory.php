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

class EntityJumpLinkButtonFactory extends AbstractEntityJumpLinkFactory {

  /**
   * Fetch a jump link
   */
  public function fetch(EntityInterface $entity) {
    return parent::fetchLinkOfType('jump_link_button', $entity);
  }

  /**
   * Create a button from an entity with a CTA field
   */
  public function create(EntityInterface $entity) {

    if (!$entity->hasField(static::CTA_FIELD) || $entity->get(static::CTA_FIELD)->isEmpty()) {
      throw new JumpLinkException('No populated CTA field');
    }

    $jumpLinkButton = new JumpLinkButton();

    $ctaValue = $entity->get(static::CTA_FIELD)->first();
    if ($ctaValue->getValue()) {
      $jumpLinkButton->setLabel($ctaValue->getValue()['title']);
    }
    if ($ctaValue->getUrl()) {
      $jumpLinkButton->setLink($ctaValue->getUrl());
    }

    if ($jumpLinkButton->isEmpty()) {
      throw new JumpLinkException('Not enough information to create a jump link button');
    }

    return $jumpLinkButton;

  }

}