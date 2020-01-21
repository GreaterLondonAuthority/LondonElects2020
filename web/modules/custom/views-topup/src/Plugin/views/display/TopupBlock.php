<?php

namespace Drupal\views_topup\Plugin\views\display;

use Drupal\views\Plugin\views\display\Block;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;

/**
 * The plugin that handles a fill block.
 *
 * @ingroup views_display_plugins
 *
 * @ViewsDisplay(
 *   id = "fill_block",
 *   title = @Translation("Top-up Block"),
 *   help = @Translation("Display the view as a block with an additional view block to top-up the results."),
 *   theme = "views_view",
 *   register_theme = FALSE,
 *   uses_hook_block = TRUE,
 *   contextual_links_locations = {"block"},
 *   admin = @Translation("Top-up Block")
 * )
 *
 * @see \Drupal\views\Plugin\Block\ViewsBlock
 * @see \Drupal\views\Plugin\Derivative\ViewsBlock
 */
class TopupBlock extends Block {

  /**
   * The display block handler returns the structure necessary for a block.
   */
  public function execute() {
    $element = $this->view->render();

    $items = $this->getOption('block_topup_items');
    $view_display = $this->getOption('block_topup_view_display');
    if ($items !== FALSE && $view_display) {
      $results = isset($element['#rows'][0]) ? count($element['#rows'][0]['#rows']) : 0;
      if ($results >= $items) {
        return $element;
      }
      list($view_name, $display_id) = explode(':', $view_display);
      $view_entity = $this->entityManager->getStorage('view')->load($view_name);
      if (!$view_entity) {
        return [];
      }
      $view = $view_entity->getExecutable();
      if (empty($view) || !$view->access($display_id)) {
        return [];
      }
      $view->setDisplay($display_id);
      $view->setItemsPerPage($items - $results);
      
      // pass the results to the view to exclude.
      if (!empty($element['#view'])) {
        $arguments = $this->getArguments($element['#view']->result);
        if(!empty($arguments)) {
          $view->setArguments([$arguments]);
        }
      }

      $output = $view->preview($display_id);
      $topupresults = isset($output['#rows'][0]) ? count($output['#rows'][0]['#rows']) : 0;
      if ($results && $topupresults && is_array($output['#rows'][0]['#rows']) && is_array($element['#rows'][0]['#rows'])) {
        $element['#rows'][0]['#rows'] = array_merge($element['#rows'][0]['#rows'], $output['#rows'][0]['#rows']);
      } else if ($topupresults && is_array($output['#rows'][0]['#rows'])) {
        $element = $output;
      }

    }

    if ($this->getOption('block_hide_empty') && empty($this->view->style_plugin->definition['even empty'])) {
      if (!isset($element['#rows']) || empty($element['#rows'][0]['#rows'])) {
        return [];
      }
    }

    return $element;
  }

  /**
   * Provide the summary for page options in the views UI.
   *
   * This output is returned as an array.
   */
  public function optionsSummary(&$categories, &$options) {
    parent::optionsSummary($categories, $options);

    $options['block_topup_items'] = [
      'category' => 'other',
      'title' => $this->t('Top-up results to'),
      'value' => ($this->getOption('block_topup_items')) ?: $this->t('Not specified'),
    ];

    $options['block_topup_relationship'] = [
      'category' => 'other',
      'title' => $this->t('Relationship field name'),
      'value' => ($this->getOption('block_topup_relationship')) ? views_ui_truncate($this->getOption('block_topup_relationship'), 24) : $this->t('Not specified'),
    ];

    $options['block_topup_view_display'] = [
      'category' => 'other',
      'title' => $this->t('View to top-up results'),
      'value' => ($this->getOption('block_topup_view_display')) ? views_ui_truncate($this->getOption('block_topup_view_display'), 24) : $this->t('Not specified'),
    ];

  }

  /**
   * Perform any necessary changes to the form values prior to storage.
   * There is no need for this function to actually store the data.
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    parent::submitOptionsForm($form, $form_state);
    $section = $form_state->get('section');
    switch ($section) {
      case 'block_topup_items':
      case 'block_topup_relationship':
      case 'block_topup_view_display':
        $this->setOption($section, $form_state->getValue($section));
        break;
    }
  }

  /**
   * Provide the default form for setting options.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    switch ($form_state->get('section')) {
      case 'block_topup_items':
        $form['#title'] .= $this->t('Number of items to top-up to');
        $form['block_topup_items'] = [
          '#type' => 'number',
          '#description' => $this->t('The number of items to populate the view up to.'),
          '#default_value' => $this->getOption('block_topup_items'),
        ];
        break;
      case 'block_topup_relationship':
        $form['#title'] .= $this->t('Relationship field name.');
        $form['block_topup_relationship'] = [
          '#type' => 'textfield',
          '#description' => $this->t('The machine of the relationship field the entity is on.'),
          '#default_value' => $this->getOption('block_topup_relationship'),
        ];
        break;

      case 'block_topup_view_display':
        $options = ['' => $this->t('-Select-')];
        $options += Views::getViewsAsOptions(FALSE, 'all', $this->view, FALSE, TRUE);
        $form['#title'] .= $this->t('View display to top-up from');
        $form['block_topup_view_display'] = [
          '#type' => 'select',
          '#description' => $this->t('The view display which will be used to pull items up to the given number'),
          '#default_value' => $this->getOption('block_topup_view_display'),
          '#options' => $options,
        ];
        break;
    }
  }

  /**
   * Returns the arguments to pass to the view.
   * @param $results
   *
   * @return string
   */
  private function getArguments($results) {
    $arguments = [];
    $relationship = $this->getOption('block_topup_relationship');
    foreach($results as $result) {
      $entity = $this->getEntity($result, $relationship);
      if($entity) {
        $entity_id = $entity->id();
        $arguments[$entity_id] = $entity_id;
      }
    }
    return implode('+', $arguments);
  }

  /**
   * Gets the entity ids from the results of a view.
   *
   * @param $row
   * @param string $relationship
   *
   * @return null
   */
  private function getEntity($row, $relationship = 'none') {
    if ($relationship === 'none') {
      return $row->_entity;
    }
    elseif (isset($row->_relationship_entities[$relationship])) {
      return $row->_relationship_entities[$relationship];
    }
    return NULL;
  }

}
