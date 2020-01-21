<?php

namespace Drupal\polling_station_lookup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 */
class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'polling_station_lookup.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('polling_station_lookup.config');
    $form['dc_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Democracy Club API key'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('dc_api.key'),
    ];
    $form['dc_api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Democracy Club base URL'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('dc_api.url'),
    ];
    $form['dc_api_ballot_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Ballot date (optional)'),
      '#description' => 'Return polling stations for a specific ballot date.',
      '#default_value' => $config->get('dc_api.ballot_date'),
    ];
    $form['gmaps_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API key'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('gmaps_api.key'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('polling_station_lookup.config')
      ->set('dc_api.key', $form_state->getValue('dc_api_key'))
      ->set('dc_api.url', $form_state->getValue('dc_api_url'))
      ->set('dc_api.ballot_date', $form_state->getValue('dc_api_ballot_date'))
      ->set('gmaps_api.key', $form_state->getValue('gmaps_api_key'))
      ->save();
  }

}
