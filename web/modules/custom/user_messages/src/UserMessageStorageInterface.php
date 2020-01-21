<?php

namespace Drupal\user_messages;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface UserMessageStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of User message revision IDs for a specific User message.
   *
   * @param \Drupal\user_messages\Entity\UserMessageInterface $entity
   *   The User message entity.
   *
   * @return int[]
   *   User message revision IDs (in ascending order).
   */
  public function revisionIds(UserMessageInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as User message author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   User message revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\user_messages\Entity\UserMessageInterface $entity
   *   The User message entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(UserMessageInterface $entity);

  /**
   * Unsets the language for all User message with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
