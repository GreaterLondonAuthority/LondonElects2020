<?php

/**
 * @file
 * Contains user_message.page.inc.
 *
 * Page callback for User message entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User message templates.
 *
 * Default template: user_message.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_message(array &$variables) {
  // Fetch UserMessage Entity Object.
  $user_message = $variables['elements']['#user_message'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
