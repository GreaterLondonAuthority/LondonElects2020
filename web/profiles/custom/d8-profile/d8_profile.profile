<?php

use Drupal\Core\Form\FormStateInterface;

function d8_profile_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {

  // Site information defaults
  $form['site_information']['site_name']['#default_value'] = 'D8 Profile';
  $form['site_information']['site_mail']['#default_value'] = 'noreply@numiko.com';

  // Account information defaults
  $form['admin_account']['account']['name']['#default_value'] = 'sysadmin';
  $form['admin_account']['account']['mail']['#default_value'] = 'dev@numiko.net';

  // Date/time settings
  $form['regional_settings']['site_default_country']['#default_value'] = 'GB';
  $form['regional_settings']['date_default_timezone']['#default_value'] = 'Europe/London';

}
