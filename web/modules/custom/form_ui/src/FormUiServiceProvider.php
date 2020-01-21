<?php

/**
 * @file
 * Contains Drupal\form_ui\FormUIServiceProvider
 */

namespace Drupal\form_ui;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the language manager service.
 */
class FormUiServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides form_validator class to alter error message.
    $definition = $container->getDefinition('form_error_handler');
    $definition->setClass('Drupal\form_ui\Form\FormErrorHandler');
  }
}