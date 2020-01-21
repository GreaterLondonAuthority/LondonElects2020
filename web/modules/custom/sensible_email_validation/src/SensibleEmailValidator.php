<?php

namespace Drupal\sensible_email_validation;

use Egulias\EmailValidator\EmailValidator;

class SensibleEmailValidator extends EmailValidator {

  /**
   * Use Drupal's default email validator, but add extra check for TLD
   *
   * {@inheritdoc}
   */
  public function isValid($email, $checkDNS = false, $strict = false) {
    $defaultValidationResult = parent::isValid($email, $checkDNS, $strict);

    if ($defaultValidationResult == TRUE) {

      $parts = $this->parser->parse((string)$email);
      if (isset($parts['domain'])) {
        if (strpos($parts['domain'], '.') !== FALSE) {
          return TRUE;
        }
      }
      return FALSE;

    } else {
      return $defaultValidationResult;
    }

  }

}
