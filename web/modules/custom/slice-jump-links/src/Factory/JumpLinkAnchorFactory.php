<?php

/**
 * @file
 * Contains factory for creating jump link items from slice.
 */

namespace Drupal\slice_jump_links\Factory;

use Drupal\Component\Utility\Html;

class JumpLinkAnchorFactory {

  /**
   * Create an anchor from a given label.
   */
  public static function createFromLabel($label) {

    return Html::getClass($label);

  }

}