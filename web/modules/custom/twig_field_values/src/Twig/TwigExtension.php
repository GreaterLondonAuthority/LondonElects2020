<?php

namespace Drupal\twig_field_values\Twig;

use Drupal\Core\Render\Markup;

/**
 * Useful Twig filters
 */
class TwigExtension extends \Twig_Extension {

  private $coreTwigExtension;

  public function __construct(\Drupal\Core\Template\TwigExtension $coreTwigExtension) {
    $this->coreTwigExtension = $coreTwigExtension;
  }

  public function getName() {
    return 'val';
  }

  public function getFilters() {
    return [
      new \Twig_SimpleFilter('field', [$this, 'fieldFilter']),
      new \Twig_SimpleFilter('field_value', [$this, 'fieldValueFilter']),
      new \Twig_SimpleFilter('bool_value', [$this, 'boolValueFilter']),
      new \Twig_SimpleFilter('array_value', [$this, 'arrayValueFilter']),
    ];
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('has_value', [$this, 'hasValueFunction']),
      new \Twig_SimpleFunction('any_has_value', [$this, 'anyHasValueFunction'])
    ];
  }

  public function anyHasValueFunction($fields) {
    return (bool) array_filter($fields, [$this, 'hasValueFunction']);
  }

  /**
   * Return true is the field is not empty.
   *
   * @param $field
   * @return bool
   */
  public function hasValueFunction($field) {
    return (bool) $this->fieldValueFilter($field);
  }

  /**
   * Strip tags to get rid of twig debugging comments and trim to get rid of extra
   * space caused by same debugging.
   *
   * Returns as Markup object to avoid double-escaping found when filters are applied
   * after render in template (like field_x|render|striptags|trim).
   *
   * @param string $field
   * @return \Drupal\Component\Render\MarkupInterface|mixed|string
   */
  public function fieldFilter($field) {
    $rendered = $this->coreTwigExtension->renderVar($field);
    if ($rendered instanceof Markup) {
      $treatedMarkup = Markup::create(trim($this->stripHtmlComments((string) $rendered)));
      return $treatedMarkup;
    } else {
      return $rendered;
    }
  }

  /**
   * Return the given string with html comments stripped out.
   *
   * @param $string
   * @return string
   */
  protected function stripHtmlComments($content) {
    return preg_replace('/<!--(.|\s)*?-->/', '', $content);
  }

  /**
   * Return the given string with all tags stripped out.
   *
   * @param $string
   * @return string
   */
  protected function stripHtmlTags($content) {
    return trim(strip_tags((string)$content));
  }

  /**
   * Get the raw value of a field. It will be auto-escaped as normal, so should
   * not be used to user-facing content. It is intended for getting values for
   * comparisons.
   *
   * @param string $field
   * @return \Drupal\Component\Render\MarkupInterface|mixed|string
   */
  public function fieldValueFilter($field) {
    $rendered = $this->fieldFilter($field);
    if (is_object($rendered)) {
      return (string) $rendered;
    } else {
      return $rendered;
    }
  }

  /**
   * Use when a boolean value is expected e.g. for comparisons
   *
   * @param string $field
   * @return \Drupal\Component\Render\MarkupInterface|mixed|string
   */
  public function boolValueFilter($field) {
    $rendered = $this->fieldFilter($field);
    if ($rendered instanceof Markup) {
      $renderedString = (string) $rendered;
    } else {
      $renderedString = $rendered;
    }
    // Strip any remaining HTML tags for boolean comparison.
    $renderedString = $this->stripHtmlTags($renderedString);

    // Deal with standard boolean formatters
    if (in_array($renderedString, ['True', 'Yes', 'On', 'Enabled'])) {
      return true;
    } else if (in_array($renderedString, ['False', 'No', 'Off', 'Disabled'])) {
      return false;
    } else {
      return (bool) $renderedString;
    }

  }

  /**
   * Returns individual items from a multi-value field as a simple array with
   * HTML comments stripped out.
   *
   * @param array $renderArray
   * @return array
   */
  public function arrayValueFilter($renderArray) {
    $arrayItems = [];
    foreach ($renderArray as $key => $item) {
      if (is_numeric($key)) {
        if (isset($item['#markup'])) {
          $arrayItems[$key] = trim($this->stripHtmlComments($item['#markup']));
        }
      }
    }
    return $arrayItems;
  }

}
