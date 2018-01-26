<?php

/**
 * @file
 * Definition of Drupal\koru_icons\Plugin\Field\FieldFormatter\KoruIconsDefaultFormatter.
 */

namespace Drupal\koru_icons\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * Plugin implementation of the 'koru_icons' formatter.
 *
 * @FieldFormatter(
 *   id = "koru_icons_default",
 *   module = "koru_icons",
 *   label = @Translation("Icons"),
 *   field_types = {
 *     "koru_icons"
 *   }
 * )
 */
class KoruIconsDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $icons = \Drupal::service('koru_icons.manager')->getSelectableIcons($this->fieldDefinition);
    foreach ($items as $delta => $item) {
      if (isset($icons[$item->value])) {
        $elements[$delta] = array('#markup' => $item->value);
      }
    }
    return $elements;
  }
}
