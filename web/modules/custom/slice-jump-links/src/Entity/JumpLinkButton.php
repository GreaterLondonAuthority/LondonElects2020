<?php

/**
 * @file
 * Contains factory for creating jump link list from node with slices.
 */

namespace Drupal\slice_jump_links\Entity;

use Drupal\slice_jump_links\Factory\JumpLinkAnchorFactory;

class JumpLinkButton extends JumpLink {

  protected $link;

  public function setLink($link) {
    $this->link = $link;
    return $this;
  }

  public function getLink() {
    if ($this->link) {
      return $this->link;
    } else {
      return '#' . $this->getAnchor();
    }
  }

  public function isEmpty() {
    if ($this->label || $this->link) {
      return false;
    }
    return true;
  }

}