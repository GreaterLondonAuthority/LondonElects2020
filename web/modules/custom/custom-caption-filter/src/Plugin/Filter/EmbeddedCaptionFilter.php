<?php

namespace Drupal\custom_caption_filter\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Utility\Xss;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Render\FilteredMarkup;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a filter to caption elements, with the <caption> element placed inside
 * the media embed template instead of the filter template (as with Drupal's
 * FilterCaption class, which this is heavily based on).
 *
 * @Filter(
 *   id = "embedded_caption_filter",
 *   title = @Translation("Caption images with caption placed within the media entity markup."),
 *   description = @Translation("Uses a caption tag within the media entity markup."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   settings = {
 *     "filter_media_class" = "media__wrapper",
 *     "filter_caption_element" = "figcaption",
 *     "filter_remove_container" = FALSE
 *   }
 * )
 */
class EmbeddedCaptionFilter extends FilterBase {

  /**
   * Whether or not we are using the admin theme.
   *
   * @var bool
   */
  protected $usingAdminTheme;

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['filter_media_class'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Media identification class'),
      '#default_value' => $this->settings['filter_media_class'],
      '#description' => $this->t('To add a custom caption filter it needs to append the caption after a given elements selector class'),
    ];
    $form['filter_caption_element'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Caption element'),
      '#default_value' => $this->settings['filter_caption_element'],
      '#description' => $this->t('The element to use for the caption'),
    ];
    $form['filter_remove_container'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove entity embed container'),
      '#default_value' => $this->settings['filter_remove_container'],
      '#description' => $this->t('Remove the entity embed container when moving the caption'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);

    if (stristr($text, 'data-caption') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);
      foreach ($xpath->query('//*[@data-caption]') as $node) {

        // Read the data-caption attribute's value, then delete it.
        $caption = Html::escape($node->getAttribute('data-caption'));
        $node->removeAttribute('data-caption');

        // Sanitize caption: decode HTML encoding, limit allowed HTML tags; only
        // allow inline tags that are allowed by default, plus <br>.
        $caption = Html::decodeEntities($caption);
        $caption = FilteredMarkup::create(Xss::filter($caption, array('a', 'em', 'strong', 'cite', 'code', 'br')));

        // The caption must be non-empty.
        if (Unicode::strlen($caption) === 0) {
          continue;
        }

        // Given the updated node and caption: re-render it with a caption, but
        // bubble up the value of the class attribute of the captioned element,
        // this allows it to collaborate with e.g. the filter_align filter.
        $tag = $node->tagName;
        $classes = $node->getAttribute('class');
        $node = ($node->parentNode->tagName === 'a') ? $node->parentNode : $node;

        $captionElement = new \DOMElement($this->settings['filter_caption_element'], $caption);

        if (!$this->usingAdminTheme()) {
          $caption = '';
          $descendant = $this->findDescendantWithClass($node, $this->settings['filter_media_class']);
          if ($descendant) {
            $descendant->parentNode->appendChild($captionElement);
          }
        }

        // We pass the unsanitized string because this is a text format
        // filter, and after filtering, we always assume the output is safe.
        // @see \Drupal\filter\Element\ProcessedText::preRenderText()
        if ($this->settings['filter_remove_container'] && $descendant->parentNode) {
          $node_filtered_markup = FilteredMarkup::create($descendant->parentNode->C14N());
        }
        else {
          $node_filtered_markup = FilteredMarkup::create($node->C14N());
        }

        $filter_caption = array(
          '#theme' => 'filter_caption',
          '#node' => $node_filtered_markup,
          '#tag' => $tag,
          '#caption' => $caption,
          '#classes' => $classes,
        );

        $altered_html = drupal_render($filter_caption);

        // Load the altered HTML into a new DOMDocument and retrieve the element.
        $updated_nodes = Html::load($altered_html)->getElementsByTagName('body')
          ->item(0)
          ->childNodes;

        foreach ($updated_nodes as $updated_node) {
          // Import the updated node from the new DOMDocument into the original
          // one, importing also the child nodes of the updated node.
          $updated_node = $dom->importNode($updated_node, TRUE);
          $node->parentNode->insertBefore($updated_node, $node);
        }
        // Finally, remove the original data-caption node.
        $node->parentNode->removeChild($node);

      }

      $result->setProcessedText(Html::serialize($dom))
        ->addAttachments(array(
          'library' => array(
            'filter/caption',
          ),
        ));
    }

    return $result;
  }

  /**
   * Recursively search for a descendant of a DOM node with a certain class.
   *
   * @param \DOMElement $node
   * @param string $className
   * @return \DOMElement
   */
  function findDescendantWithClass(\DOMElement $node, $className) {
    if ($node->hasChildNodes()) {
      foreach ($node->childNodes as $child) {
        if ($child instanceof \DOMElement) {

          if ($child->getAttribute('class') && strpos(
              $child->getAttribute('class'),
              $className
            ) !== FALSE
          ) {
            return $child;
          }
          else {
            return $this->findDescendantWithClass($child, $className);
          }

        }
      }
    }
    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return '';
  }

  protected function usingAdminTheme() {
    if (!isset($this->usingAdminTheme)) {
      $this->usingAdminTheme = (\Drupal::service('theme.manager')->getActiveTheme()->getName() == \Drupal::config('system.theme')->get('admin'));
    }
    return $this->usingAdminTheme;
  }

}
