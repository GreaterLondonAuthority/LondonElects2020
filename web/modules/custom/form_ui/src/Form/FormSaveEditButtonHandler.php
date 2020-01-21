<?php

namespace Drupal\form_ui\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an additional save & edit button.
 */
class FormSaveEditButtonHandler {

  /**
   * Alter the form to add a 'Save & continue editing' button.
   */
  public function formAlter(array &$form, FormStateInterface $form_state, $form_id) {
    $form['actions']['submitedit'] = [
      '#type' => 'submit',
      '#value' => t("Save & Continue Editing"),
      '#access' => TRUE,
      '#weight' => 10,
      '#submit' => [
        '::submitForm',
        '::save',
        [FormSaveEditButtonHandler::class, 'saveAndEditRedirect']
      ]
    ];
  }

  /**
   * After clicking save & edit the user is redirected back to the form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function saveAndEditRedirect(array &$form, FormStateInterface $form_state) {
    /* @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();
    $entity_type_id = $entity->getEntityTypeId();
    $form_state->setRedirect("entity.$entity_type_id.edit_form", [$entity_type_id => $entity->id()]);
    // To ensure consistent behaviour we must drop the detination parameter
    // otherwise this will override our expected bahaviour.
    \Drupal::request()->query->remove('destination');
  }

}
