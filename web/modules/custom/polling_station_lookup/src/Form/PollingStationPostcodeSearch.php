<?php

namespace Drupal\polling_station_lookup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\url;

class PollingStationPostcodeSearch extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'polling_station_postcode_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    $form['postcode_search'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter a postcode'),
      '#title_display' => 'invisible',
      '#placeholder' => ('Enter a postcode, i.e. SE1 2AA'),
      '#required' => TRUE,
    );


    $form['actions_search']['#type'] = 'actions';

    $form['actions_search']['postcode_search_submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit postcode'),
      '#button_type' => 'primary',
    );

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $path = URL::fromUserInput('/polling-stations/'.$form_state->getValue('postcode_search'));

    $form_state->setRedirectUrl($path);

  }

}