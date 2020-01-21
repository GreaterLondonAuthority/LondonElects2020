<?php

/**
 * @file
 * Contains factory for creating jump link list from node with slices.
 */

namespace Drupal\slice_jump_links\Entity;

use Drupal\slice_jump_links\Factory\JumpLinkAnchorFactory;

class JumpLink {

  protected $label;
  protected $anchor;

  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  public function getLabel() {
    return $this->label;
  }

  public function setAnchor($anchor) {
    $this->anchor = $anchor;
    return $this;
  }

  public function getAnchor() {
    if (!$this->anchor) {
      $this->setAnchor(\Drupal::Service('jump_link.anchor.factory')->createFromLabel($this->getLabel()));
    }
    return $this->anchor;
  }

  public function justAnchor() {
    if (!$this->label) {
      return true;
    }
    return false;
  }

  public function isEmpty() {
    if ($this->label || $this->anchor) {
      return false;
    }
    return true;
  }

}