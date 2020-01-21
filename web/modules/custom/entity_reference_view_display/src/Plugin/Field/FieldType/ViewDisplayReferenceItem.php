<?php

namespace Drupal\entity_reference_view_display\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\views\Views;
use Drupal\views\Entity\View;


/**
 * Plugin implementation of the 'view_display_reference_item' field type.
 *
 * @FieldType(
 *   id = "view_display_reference_item",
 *   label = @Translation("View Display"),
 *   description = @Translation("View display reference"),
 *   category = @Translation("Reference"),
 *   default_widget = "view_display_options",
 *   default_formatter = "view_display_rendered",
 * )
 */
class ViewDisplayReferenceItem extends FieldItemBase implements OptionsProviderInterface {

  const VIEW_DISPLAY_SEPERATOR = ':';

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
       'allowed_values' => array(),
    ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'target_id' => array(
          'type' => 'varchar',
          'length' => 255,
        ),
      ),
      'indexes' => array(
        'target_id' => array('target_id'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['target_id'] = DataDefinition::create('string')
      ->setLabel(t('Text'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'target_id';
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $allowed_values = $this->getSetting('allowed_values');
    $element['allowed_values'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Allowed options'),
      '#options' => $this->getPossibleOptions(),
      '#default_value' => $allowed_values,
      '#rows' => 10,
      '#field_name' => $this->getFieldDefinition()->getName(),
      '#entity_type' => $this->getEntity()->getEntityTypeId(),
      '#allowed_values' => $allowed_values,
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return $this->getSettableValues($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $views = Views::getAllViews();
    $views_displays = array();
    foreach ($views as $view_id => $view) {
      $views_displays = array_merge($views_displays, $this->getViewDisplays($view));
    }

    $view_bundle = 'view';
    $return[$view_bundle] = $views_displays;

    return count($return) == 1 ? reset($return) : $return;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // Flatten options first, because "settable options" may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getSettableOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $views = Views::getAllViews();
    $views_displays = array();
    $allowed_view_displays = $this->getAllowedViewDisplays();
    foreach ($views as $view_id => $view) {
      $views_displays = array_merge($views_displays, $this->getViewDisplays($view, $allowed_view_displays));
    }

    $view_bundle = 'view';
    $return[$view_bundle] = $views_displays;

    return count($return) == 1 ? reset($return) : $return;
  }

  /**
   * Get an array of allowed view displays
   *
   * @see getSettableOptions
   */
  protected function getAllowedViewDisplays() {
    $allowed_values = $this->getSetting('allowed_values');
    return array_filter($allowed_values);
  }

  /**
   * Get an array of view displays joined by VIEW_DISPLAY_SEPERATOR
   *
   * @see getSettableOptions
   */
  protected function getViewDisplays(View $view, $allowed_view_displays = array()) {
    $all_displays = $view->get('display');
    $view_id = $view->id();
    $view_label = $view->label();
    $block_displays = array_filter($all_displays, array($this, 'isDisplayBlock'));

    $view_displays = array();
    foreach ($block_displays as $display_id => $display) {
      $target_id = $view_id . self::VIEW_DISPLAY_SEPERATOR . $display_id;
      if (empty($allowed_view_displays) || isset($allowed_view_displays[$target_id])) {
        $display_label = $view_label . ': ' . $display['display_title'];
        $view_displays[$target_id] = $display_label;
      }
    }
    return $view_displays;
  }

  /**
   * Function for array_filter to remove non-block view displays.
   *
   * As well as core 'block's, also allow 'fill_block's from the views-topup module.
   *
   * @see getViewDisplays
   */
  protected function isDisplayBlock(array $view_display) {
    return ($view_display['display_plugin'] == 'block' || $view_display['display_plugin'] == 'fill_block');
  }

}
