<?php

namespace Drupal\slice_jump_links\Exception;

/**
 * This exception is thrown when a jump link cannot be created.
 */
class JumpLinkException extends \Exception {

  /**
   * Constructs a JumpLinkException object.
   *
   * @param string $message
   *   The message for the exception.
   */
  public function __construct($message = NULL) {
    parent::__construct($message);
  }

}