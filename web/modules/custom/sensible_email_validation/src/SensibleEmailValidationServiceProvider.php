<?php

namespace Drupal\sensible_email_validation;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class SensibleEmailValidationServiceProvider extends ServiceProviderBase {

  /**
   * Replace default email validator with our own
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('email.validator');
    $definition->setClass('Drupal\sensible_email_validation\SensibleEmailValidator');
  }

}
