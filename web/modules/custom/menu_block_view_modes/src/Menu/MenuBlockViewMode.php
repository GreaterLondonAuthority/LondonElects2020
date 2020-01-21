<?php

namespace Drupal\menu_block_view_modes\Menu;

class MenuBlockViewMode {

  protected $renderLevel;
  protected $viewMode;

  const STARTING_LEVEL = 1;

  public function __construct($renderLevel, $viewMode) {
    $this->renderLevel = $renderLevel;
    $this->viewMode = $viewMode;
  }

  public function buildItems(&$build) {
    if (isset($build['#items'])) {
      $this->buildItemsAtLevel($build['#items'], self::STARTING_LEVEL);
    }
  }

  /**
   * A recursive function to work through the menu items and either 
   * continue the build or render, depending on the level.
   */
  protected function buildItemsAtLevel(&$items, $currentLevel) {
    $ids = $entities = [];

    if ($currentLevel == $this->renderLevel) {
      $this->renderItemsAtLevel($items);
    } else {
      foreach ($items as &$item) {
        $this->buildItemsAtLevel($item['below'], $currentLevel + 1);
      }
    }
  }

  /**
   * Instead of continuing with the menu build add a rendered entity array for each of the menu items.
   */
  protected function renderItemsAtLevel(&$items) {
    $ids = $this->getItemEntityIds($items);
    foreach ($ids as $entityType => $entityIds) {
      $entities[$entityType] = $this->loadMultipleEntities($entityType, $entityIds);
    }
    foreach ($items as &$item) {
      if (isset($item['entity_id']) && isset($entities[$item['entity_type']][$item['entity_id']])) {
        $entity = $entities[$item['entity_type']][$item['entity_id']];
        $item['bundle'] = $entity->bundle();
        $item['entity'] = $this->renderEntityToViewMode($item['entity_type'], $entity);
      }
    }
  }

  /**
   * Get the entity ids for the menu items.
   */
  protected function getItemEntityIds(&$items) {
    $ids = [];
    foreach ($items as &$item) {
      if ($item['url']->isRouted()) {
        $params = $item['url']->getRouteParameters();
        if (isset($params)) {
          foreach ($params as $entity_type => $entity_id) {
            $item['entity_type'] = $entity_type;
            $ids[$entity_type][] = $item['entity_id'] = $entity_id;
          }
        }
      }
    }
    return $ids;
  }

  /**
   * Use the entity load multiple for the given entity type.
   */
  protected function loadMultipleEntities($entityType, $entityIds) {
    $storage = \Drupal::service('entity_type.manager')->getStorage($entityType);
    return $storage->loadMultiple($entityIds);
  }

  /**
   * Get the render array for a given entity to the view mode set on the MenuBlockViewMode
   */
  protected function renderEntityToViewMode($entityType, $entity) {
    $viewModeOptions = \Drupal::service('entity_display.repository')->getViewModeOptionsByBundle($entityType, $entity->bundle());
    if (array_key_exists($this->viewMode, $viewModeOptions)) {
      $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);
      return $viewBuilder->view($entity, $this->viewMode);
    }
  }

}