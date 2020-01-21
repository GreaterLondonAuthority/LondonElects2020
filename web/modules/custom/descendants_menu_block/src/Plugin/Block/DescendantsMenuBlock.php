<?php

namespace Drupal\descendants_menu_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Entity\Menu;

use Drupal\menu_block\Plugin\Block\MenuBlock;


/**
 * Provides a 'DescendantsMenuBlock' block.
 *
 * @Block(
 *  id = "descendants_menu_block",
 *  admin_label = @Translation("Descendants menu block"),
 *  category = @Translation("Descendants menu"),
 *  deriver = "Drupal\descendants_menu_block\Plugin\Derivative\DescendantsMenuBlock"
 * )
 */
class DescendantsMenuBlock extends MenuBlock {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->configuration;
    $defaults = $this->defaultConfiguration();

    $form = parent::blockForm($form, $form_state);

    // Default "Expand all menu links" to true
    $form['advanced']['expand']['#default_value'] = isset($config['expand']) ? $config['expand'] : $defaults['expand'];
    $form['advanced']['expand']['#description'] .= ' ' . $this->t('This must be checked to display descendant links.');

    $form['descendants'] = [
      '#type' => 'details',
      '#title' => $this->t('Descendants options'),
      '#open' => FALSE,
      '#process' => [[get_class(), 'processBlockFieldSets']],
    ];

    $form['descendants']['use_current_page'] = array(
      '#type' => 'checkbox',
      '#title' => '<strong>' . $this->t('Descendants of current page') . '</strong>',
      '#description' => $this->t('Sets fixed parent to the current page and displays child links to the maximum set in "Menu levels".'),
      '#default_value' => isset($config['use_current_page']) ? $config['use_current_page'] : $defaults['use_current_page'],
      '#weight' => '10',
    );

    return $form;
  }

  /**
   * Form API callback: Processes the elements in field sets.
   *
   * Adjusts the #parents of field sets to save its children at the top level.
   */
  public static function processBlockFieldSets(&$element, FormStateInterface $form_state, &$complete_form) {
    array_pop($element['#parents']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['use_current_page'] = $form_state->getValue('use_current_page');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configuration;

    if ($config['use_current_page']) {
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node) {
        $nid = $node->id();
        $parent_plugin_id = $this->getParentPluginID($nid);
        // If we are unable to find node in the menu then we will not show the menu block.
        if (!$parent_plugin_id) {
          return [];
        }
        $config['parent'] = $parent_plugin_id;
        $this->setConfigurationValue('parent', $config['parent']);
      }
    }

    $build = parent::build();

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'use_current_page' => 1,
      'expand' => 1,
      'suggestion' => 'descendant',
    ];
  }

  /**
   * {@inheritdoc}
   */
  private function getParentPluginID($nid) {
    $config = $this->configuration;
    $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
    $links = $menu_link_manager->loadLinksByRoute('entity.node.canonical', array('node' => $nid));
    // Return the first result that matches parent in config.
    foreach ($links as $link) {
      $definition = $link->getPluginDefinition();
      if ($config['parent'] == ($definition['menu_name'] . ':')) {
        return $link->getPluginId();
      }
    }
    return NULL;
  }

}
