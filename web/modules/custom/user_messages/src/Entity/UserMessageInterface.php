<?php

namespace Drupal\user_messages\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User message entities.
 *
 * @ingroup user_messages
 */
interface UserMessageInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User message name.
   *
   * @return string
   *   Name of the User message.
   */
  public function getName();

  /**
   * Sets the User message name.
   *
   * @param string $name
   *   The User message name.
   *
   * @return \Drupal\user_messages\Entity\UserMessageInterface
   *   The called User message entity.
   */
  public function setName($name);

  /**
   * Gets the User message creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User message.
   */
  public function getCreatedTime();

  /**
   * Sets the User message creation timestamp.
   *
   * @param int $timestamp
   *   The User message creation timestamp.
   *
   * @return \Drupal\user_messages\Entity\UserMessageInterface
   *   The called User message entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the User message revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the User message revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\user_messages\Entity\UserMessageInterface
   *   The called User message entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the User message revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the User message revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\user_messages\Entity\UserMessageInterface
   *   The called User message entity.
   */
  public function setRevisionUserId($uid);

}
