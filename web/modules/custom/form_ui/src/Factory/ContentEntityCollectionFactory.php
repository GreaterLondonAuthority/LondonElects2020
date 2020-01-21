<?php

namespace Drupal\form_ui\Factory;

class ContentEntityCollectionFactory {
  
  public static function getOptions() {
    $options = array();
    $options['media'] = 'Media';
    $options['node'] = 'Content';
    foreach (\Drupal::entityTypeManager()->getDefinitions() as $id => $definition) {
      if (is_a($definition ,'Drupal\Core\Entity\ContentEntityType')) {
        if ($definition->hasLinkTemplate('collection')) {
          $options[$id] = $definition->getLabel();
        }
      }
    }
    return $options;
  }

  public static function getOptionUrl($entityType) {
    switch ($entityType) {
      case 'media':
        return 'admin/content/media';
        break;
      case 'node':
        return 'admin/content';
        break;
      default:
        $definition = \Drupal::entityTypeManager()->getDefinition($entityType);
        if ($definition->hasLinkTemplate('collection')) {
          return $definition->getLinkTemplate('collection');
        }
        break;
    }
  }

}