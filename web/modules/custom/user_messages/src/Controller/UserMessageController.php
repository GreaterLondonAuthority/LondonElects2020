<?php

namespace Drupal\user_messages\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\user_messages\Entity\UserMessageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserMessageController.
 *
 *  Returns responses for User message routes.
 */
class UserMessageController extends ControllerBase implements ContainerInjectionInterface {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new UserMessageController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a User message revision.
   *
   * @param int $user_message_revision
   *   The User message revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($user_message_revision) {
    $user_message = $this->entityTypeManager()->getStorage('user_message')
      ->loadRevision($user_message_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('user_message');

    return $view_builder->view($user_message);
  }

  /**
   * Page title callback for a User message revision.
   *
   * @param int $user_message_revision
   *   The User message revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($user_message_revision) {
    $user_message = $this->entityTypeManager()->getStorage('user_message')
      ->loadRevision($user_message_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $user_message->label(),
      '%date' => $this->dateFormatter->format($user_message->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a User message.
   *
   * @param \Drupal\user_messages\Entity\UserMessageInterface $user_message
   *   A User message object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(UserMessageInterface $user_message) {
    $account = $this->currentUser();
    $user_message_storage = $this->entityTypeManager()->getStorage('user_message');

    $langcode = $user_message->language()->getId();
    $langname = $user_message->language()->getName();
    $languages = $user_message->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $user_message->label()]) : $this->t('Revisions for %title', ['%title' => $user_message->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all user message revisions") || $account->hasPermission('administer user message entities')));
    $delete_permission = (($account->hasPermission("delete all user message revisions") || $account->hasPermission('administer user message entities')));

    $rows = [];

    $vids = $user_message_storage->revisionIds($user_message);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\user_messages\UserMessageInterface $revision */
      $revision = $user_message_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $user_message->getRevisionId()) {
          $link = $this->l($date, new Url('entity.user_message.revision', [
            'user_message' => $user_message->id(),
            'user_message_revision' => $vid,
          ]));
        }
        else {
          $link = $user_message->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.user_message.translation_revert', [
                'user_message' => $user_message->id(),
                'user_message_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.user_message.revision_revert', [
                'user_message' => $user_message->id(),
                'user_message_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.user_message.revision_delete', [
                'user_message' => $user_message->id(),
                'user_message_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['user_message_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
