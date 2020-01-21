<?php

namespace Drupal\form_ui\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use Drupal\form_ui\Factory\ContentEntityCollectionFactory;

/**
 * Class ContentTypeForm.
 *
 * @package Drupal\section_node\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'form_ui.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('form_ui.settings');

    $values = array();
    $options = ContentEntityCollectionFactory::getOptions();

    $form['user'] = [
      '#type' => 'details',
      '#title' => $this->t('User'),
      '#open' => true,
    ];

    $form['user']['user_one_time_login_enum'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove username from one-time login page'),
      '#description' => $this->t('Reduces the potential for using the link as a method of finding a username from id'),
      '#default_value' => $config->get('user.one_time_login_enum'),
    ];

    $form['edit_form'] = [
      '#type' => 'details',
      '#title' => $this->t('Entity Form'),
      '#open' => true,
    ];

    $form['edit_form']['edit_form_styling'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply smaller screen styling alterations'),
      '#description' => $this->t('Including reduced breakpoint'),
      '#default_value' => $config->get('edit_form.styling'),
    ];

    $form['edit_form']['hide_preview'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Always hide preview button'),
      '#description' => $this->t('This shows inconsistently due to a core bug, and does not seem to be compatible with media. Workbench Moderation drafts provide "drafts" are adequate for previewing'),
      '#default_value' => $config->get('edit_form.hide_preview'),
    ];

    $form['edit_form']['save_edit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show a save and edit button'),
      '#description' => $this->t('Show a save and edit button to allow the user to save and continue editing the node'),
      '#default_value' => $config->get('edit_form.save_edit'),
    ];

    $form['edit_form']['form_error_handler'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply Form UI Error Handler to increase detail in form error messages'),
      '#description' => $this->t('This includes adding the tab to the error message where applicable'),
      '#default_value' => $config->get('edit_form.form_error_handler'),
    ];

    $values = array();
    foreach ($options as $option_id => $option) {
      if ($config->get('edit_form.novalidate.' . $option_id)) {
        $values[] = $option_id;
      }
    }

    $form['edit_form']['edit_form_add_novalidate'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Disable HTML5 validation for the following edit forms (adds novalidate)'),
      '#options' => $options,
      '#default_value' => $values,
    ];

    $form['manage_display'] = [
      '#type' => 'details',
      '#title' => $this->t('Manage Display'),
      '#open' => true,
    ];

    $form['manage_display']['display_field_ids'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the field ids on the Manage Display form'),
      '#description' => $this->t('These will appear alongside the existing field name'),
      '#default_value' => $config->get('manage_display.display_field_ids'),
    ];

    $form['redirects'] = [
      '#type' => 'details',
      '#title' => $this->t('Manage Redirects'),
      '#open' => true,
    ];

    $form['redirects']['redirect_editor_login'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Redirect editor login to content overview'),
      '#description' => $this->t("For those users with 'access content overview' permission"),
      '#default_value' => $config->get('redirects.editor_login'),
    ];

    $form['redirects']['redirect_workbench_login'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Redirect workbench user login to workbench overview'),
      '#description' => $this->t("For those users with 'access workbench' permission"),
      '#default_value' => $config->get('redirects.workbench_login'),
    ];

    $values = array();
    foreach ($options as $option_id => $option) {
      if ($config->get('redirects.edit_form.' . $option_id)) {
        $values[] = $option_id;
      }
    }

    $form['redirects']['redirect_entity_edit'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Redirect the selected entities after edit back to listings'),
      '#options' => $options,
      '#default_value' => $values,
    ];

    $form['toolbar'] = [
      '#type' => 'details',
      '#title' => $this->t('Manage Toolbar'),
      '#open' => true,
    ];

    $form['toolbar']['toolbar_home_button'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the home button on the toolbar'),
      '#description' => $this->t('While browsing the site'),
      '#default_value' => $config->get('toolbar.home_button'),
    ];

    $form['toolbar']['toolbar_shortcuts_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shortcuts title override'),
      '#default_value' => $config->get('toolbar.shortcuts_label'),
    ];

    $form['fields'] = [
      '#type' => 'details',
      '#title' => $this->t('Fields'),
      '#open' => true,
    ];

    $form['fields']['fields_description_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Display placement of field description'),
      '#options' => array(
        'before' => 'Before',
        'after' => 'After',
      ),
      '#default_value' => $config->get('fields.description_display'),
    ];

    $form['comments'] = [
      '#type' => 'details',
      '#title' => $this->t('Comments'),
      '#open' => true,
    ];

    $form['comments']['comments_author_mail_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Re-label the comments author e-mail field title'),
      '#default_value' => $config->get('comments.author_mail_title'),
    ];

    $form['comments']['comments_author_mail_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Re-label the comments author e-mail field description'),
      '#description' => $this->t('You can also add &lt;none&gt; to remove the description'),
      '#default_value' => $config->get('comments.author_mail_description'),
    ];

    $form['comments']['comments_save_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Re-label the comments save button'),
      '#default_value' => $config->get('comments.save_label'),
    ];

    $form['comments']['comments_hide_homepage_field'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide homepage field'),
      '#description' => $this->t('Hide the homepage field when anonymous contact details are required'),
      '#default_value' => $config->get('comments.hide_homepage_field'),
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

    $config = $this->config('form_ui.settings');

    $config->set('user.one_time_login_enum', $form_state->getValue('user_one_time_login_enum'));
    $config->set('edit_form.styling', $form_state->getValue('edit_form_styling'));
    $config->set('edit_form.form_error_handler', $form_state->getValue('form_error_handler'));
    $config->set('edit_form.hide_preview', $form_state->getValue('hide_preview'));
    $config->set('edit_form.save_edit', $form_state->getValue('save_edit'));
    $config->set('manage_display.display_field_ids', $form_state->getValue('display_field_ids'));
    $config->set('redirects.editor_login', $form_state->getValue('redirect_editor_login'));
    $config->set('redirects.workbench_login', $form_state->getValue('redirect_workbench_login'));
    $config->set('toolbar.home_button', $form_state->getValue('toolbar_home_button'));
    $config->set('toolbar.shortcuts_label', $form_state->getValue('toolbar_shortcuts_label'));
    $config->set('fields.description_display', $form_state->getValue('fields_description_display'));
    $config->set('comments.save_label', $form_state->getValue('comments_save_label'));
    $config->set('comments.author_mail_title', $form_state->getValue('comments_author_mail_title'));
    $config->set('comments.author_mail_description', $form_state->getValue('comments_author_mail_description'));
    $config->set('comments.hide_homepage_field', $form_state->getValue('comments_hide_homepage_field'));

    $redirect_entity_edit_values = array_filter($form_state->getValue('redirect_entity_edit'));
    $novalidate_entity_edit_values = array_filter($form_state->getValue('edit_form_add_novalidate'));
    foreach (ContentEntityCollectionFactory::getOptions() as $id => $label) {
      if (in_array($id, $redirect_entity_edit_values)) {
        $config->set('redirects.edit_form.' . $id, ContentEntityCollectionFactory::getOptionUrl($id));
      } else {
        $config->clear('redirects.edit_form.' . $id);
      }
      if (in_array($id, $novalidate_entity_edit_values)) {
        $config->set('edit_form.novalidate.' . $id, $id);
      } else {
        $config->clear('edit_form.novalidate.' . $id);
      }
    }

    $config->save();
  }

}
