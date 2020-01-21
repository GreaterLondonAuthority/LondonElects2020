<?php

namespace Drupal\icon_menu\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Entity\Menu;
use Drupal\system\Plugin\Block\SystemMenuBlock;

/**
 * Provides an extended Menu block with icon functionality.
 *
 * @Block(
 *   id = "icon_menu_block",
 *   admin_label = @Translation("Icon menu block"),
 *   category = @Translation("Icon Menus"),
 *   deriver = "Drupal\system\Plugin\Derivative\SystemMenuBlock"
 * )
 */
class IconMenuBlock extends SystemMenuBlock {

  public function build() {
    $build = parent::build();
    if (!isset($build['#items'])) {
      return $build;
    }
    $build['#theme'] = 'icon_menu';
    foreach ($build['#items'] as &$item) {
      if ($attributes = $item['url']->getOption('attributes')) {
        $item['icon'] = $attributes['title'];
        unset($attributes['title']);
        $item['url']->setOption('attributes', $attributes);
      }
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->menuStorage->loadMultiple() as $menu => $entity) {
      $this->derivatives[$menu] = $base_plugin_definition;
      $this->derivatives[$menu]['admin_label'] = $entity->label();
      $this->derivatives[$menu]['config_dependencies']['config'] = array($entity->getConfigDependencyName());
    }
    return $this->derivatives;
  }

}