<?php

namespace Drupal\form_ui\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormErrorHandler as FormErrorHandlerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\form_ui\Element\ErrorFormat;

/**
 * Provides validation of form submissions.
 */
class FormErrorHandler extends FormErrorHandlerBase {

  /**
   * Loops through and displays all form errors.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function displayErrorMessages(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('form_ui.settings');
    if ($config->get('edit_form.form_error_handler')) {
      $this->displayDetailedErrorMessages($form, $form_state);
    } else {
      parent::displayErrorMessages($form, $form_state);
    }
  }

  /**
   * Display the error message in the improved format.
   */
  protected function displayDetailedErrorMessages($form, FormStateInterface $form_state) {
    $errors = $form_state->getErrors();
    $errorFormat = new ErrorFormat($form_state);

    // Loop through all form errors and set an error message.
    foreach ($errors as $key => $error) {
      if ($error instanceof TranslatableMarkup) {
        $error = $errorFormat->formatError($key, $error);
      }
      $this
        ->messenger()
        ->addMessage($error, 'error');
    }
  }

}
