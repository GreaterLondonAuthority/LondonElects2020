<?php

namespace Drupal\user_messages\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class UserMessageTypeForm.
 */
class UserMessageTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $user_message_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $user_message_type->label(),
      '#description' => $this->t("Label for the User message type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $user_message_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\user_messages\Entity\UserMessageType::load',
      ],
      '#disabled' => !$user_message_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $user_message_type = $this->entity;
    $status = $user_message_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label User message type.', [
          '%label' => $user_message_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label User message type.', [
          '%label' => $user_message_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($user_message_type->toUrl('collection'));
  }

}
