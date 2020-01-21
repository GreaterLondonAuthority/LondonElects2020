<?php

namespace Drupal\form_ui\Element;

use Drupal\Core\Form\FormStateInterface;

/**
 * Handles error formatting for Form UI
 */
class ErrorFormat {

  const FORMAT_TITLE = 'The <b>@title field</b>';
  const FORMAT_TITLE_WITH_NUMBER = 'The <b>@number item</b> in the <b>@title field</b>';
  const FORMAT_FIELD_GROUP = ' (in the <b>@group @group_type</b>)';

  const PARENT_FIELD_GROUP_TYPE = 'tab';

  protected $complete_form;

  public function __construct(FormStateInterface $form_state) {
    $this->complete_form = $form_state->getCompleteForm();
  }

  /**
   * Return the improved more detailed error message
   */
  public function formatError($key, $error) {
    $error_arguments = $error->getArguments();
    $field_message = self::FORMAT_TITLE;
    $field_arguments['@title'] = $error_arguments['@name'];
    if ($error_arguments['@name'] instanceof TranslatableMarkup) {
      $field_arguments = $error_arguments['@name']->getArguments();
      if (isset($field_arguments['@number'])) {
        $field_message = self::FORMAT_TITLE_WITH_NUMBER;
        $field_arguments['@number'] = $this->getOrdinalNumber($field_arguments['@number']);
      }
    }    
    if ($group = $this->getFieldGroupLabel($key)) {
      $field_message .= self::FORMAT_FIELD_GROUP;
      $field_arguments['@group'] = $group;
      $field_arguments['@group_type'] = self::PARENT_FIELD_GROUP_TYPE;
    }
    $translation = \Drupal::service('string_translation');
    $error_arguments['@name'] = $translation->translate($field_message, $field_arguments);
    return $translation->translate($this->getOriginalErrorMessage($error), $error_arguments);
  }

  /**
   * Get the original error message and remove the addiitonal 'field' text
   */
  protected function getOriginalErrorMessage($error) {
    return str_replace('field ', '', $error->getUntranslatedString());
  }

  /**
   * Get the number ordinal for the given number
   * (e.g. 1 => 1st, 3 => 3rd, etc)
   */
  protected function getOrdinalNumber($number) {
    $formatter = \NumberFormatter::create('en_GB', \NumberFormatter::ORDINAL);
    return $formatter->format($number);
  }

  /**
   * Recursively go up through the groups to find the top level tab
   * (this will only work when using vertial tabs)
   */
  protected function getFieldGroupLabel($key) {
    $field_id = $this->getFieldIdFromKey($key);
    if (isset($this->complete_form['#group_children']) && isset($this->complete_form['#group_children'][$field_id])) {
      $group_id = $this->complete_form['#group_children'][$field_id];
      if ($this->complete_form['#fieldgroups'][$group_id]->format_type == self::PARENT_FIELD_GROUP_TYPE) {
        return $this->complete_form['#fieldgroups'][$group_id]->label;
      } else {
        return $this->getFieldGroupLabel($group_id);
      }
    }
  }

  /**
   * Get the field id (parent) from the key
   */
  protected function getFieldIdFromKey($key) {
    $parents = explode('][', $key);
    return $parents[0];
  } 

}