<?php

namespace Drupal\user_messages\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the User message type entity.
 *
 * @ConfigEntityType(
 *   id = "user_message_type",
 *   label = @Translation("User message type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\user_messages\UserMessageTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\user_messages\Form\UserMessageTypeForm",
 *       "edit" = "Drupal\user_messages\Form\UserMessageTypeForm",
 *       "delete" = "Drupal\user_messages\Form\UserMessageTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\user_messages\UserMessageTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "user_message_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "user_message",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/user_message_type/{user_message_type}",
 *     "add-form" = "/admin/structure/user_message_type/add",
 *     "edit-form" = "/admin/structure/user_message_type/{user_message_type}/edit",
 *     "delete-form" = "/admin/structure/user_message_type/{user_message_type}/delete",
 *     "collection" = "/admin/structure/user_message_type"
 *   }
 * )
 */
class UserMessageType extends ConfigEntityBundleBase implements UserMessageTypeInterface {

  /**
   * The User message type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The User message type label.
   *
   * @var string
   */
  protected $label;

}
