<?php

namespace Drupal\user_messages;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\user_messages\Entity\UserMessageInterface;

/**
 * Defines the storage handler class for User message entities.
 *
 * This extends the base storage class, adding required special handling for
 * User message entities.
 *
 * @ingroup user_messages
 */
class UserMessageStorage extends SqlContentEntityStorage implements UserMessageStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(UserMessageInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {user_message_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {user_message_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(UserMessageInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {user_message_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('user_message_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
