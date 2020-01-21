<?php

namespace Drupal\form_ui\Element;

use Drupal\filter\Element\TextFormat as BaseTextFormat;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a text format render element.
 * This inherits directly from the Filter module text format
 * with the exception of moving the description within the field
 * within the Filter module text_format the description is left at wrapper level
 * which only works if you want the description after the field.
 *
 * Properties:
 * - #base_type: The form element #type to use for the 'value' element.
 *   'textarea' by default.
 * - #format: (optional) The text format ID to preselect. If omitted, the
 *   default format for the current user will be used.
 * - #allowed_formats: (optional) An array of text format IDs that are available
 *   for this element. If omitted, all text formats that the current user has
 *   access to will be allowed.
 *
 * Usage Example:
 * @code
 * $form['body'] = array(
 *   '#type' => 'text_format',
 *   '#title' => 'Body',
 *   '#format' => 'full_html',
 *   '#default_value' => '<p>The quick brown fox jumped over the lazy dog.</p>',
 * );
 * @endcode
 *
 * @see \Drupal\Core\Render\Element\Textarea
 *
 * @RenderElement("text_format")
 */
class TextFormat extends BaseTextFormat {

    public static function processFormat(&$element, FormStateInterface $form_state, &$complete_form) {
        $element = parent::processFormat($element, $form_state, $complete_form);

        if (isset($element['#description'])) {
          $element['value']['#description'] = $element['#description'];
          unset($element['#description']);
        }

        return $element;
    }

}