<?php

namespace Drupal\form_ui\Factory;

use Drupal\text\Plugin\Field\FieldWidget\TextareaWithSummaryWidget;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteTagsWidget;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;

class FieldElementRenderFactory {

    /** 
     * Get an array of references to the form elements actual field render array.
     */
    public static function getElementRenderArrayKeys($widget, $render_element) {
        $field_elements = array();
        // Special case for fields with an additional summary field
        if (isset($render_element['summary'])) {
            $field_elements[] = 'summary';
        }
        if ($widget instanceof EntityReferenceAutocompleteWidget) {
            $field_elements[] = 'target_id';
        } else if ($widget instanceof TextareaWithSummaryWidget) {
            $field_elements[] = 'root';
        } else if ($widget instanceof EntityReferenceAutocompleteTagsWidget) {
            $field_elements[] = 'target_id';
        } else if ($widget instanceof LinkWidget) {
            $field_elements[] = 'uri';
        } else if (isset($render_element['value'])) {
            $field_elements[] = 'value';
        } else {
            $field_elements[] = 'root';
        }
        return $field_elements;
    }

}