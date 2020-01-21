<?php

/**
 * @file
 * Contains a list of jump links (with CTA functionality).
 */

namespace Drupal\slice_jump_links\ItemList;

use Drupal\slice_jump_links\Entity\JumpLink;
use Drupal\slice_jump_links\Entity\JumpLinkButton;

class JumpLinkItemList {

  protected $items = [];

  protected $buttons = [];

  public function addItem(JumpLink $item) {
    $this->items[] = $item;
    return $this;
  }

  public function getItems() {
    return $this->items;
  }

  public function addButton(JumpLinkButton $button) {
    $this->buttons[] = $button;
    return $this;
  }

  public function getButtons() {
    return $this->buttons;
  }

  public function isEmpty() {
    return (empty($this->items) && empty($this->buttons)) ? true : false;
  }

}