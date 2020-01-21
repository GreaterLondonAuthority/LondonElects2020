<?php

namespace Drupal\user_messages;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the User message entity.
 *
 * @see \Drupal\user_messages\Entity\UserMessage.
 */
class UserMessageAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\user_messages\Entity\UserMessageInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished user message entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published user message entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit user message entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete user message entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add user message entities');
  }

}
