<?php

namespace Drupal\koru_icons\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;


/**
 * Plugin implementation of the 'koru_icons_default' widget.
 *
 * @FieldWidget(
 *   id = "koru_icons_default",
 *   label = @Translation("NCAR|UCAR Icon Select Options"),
 *   field_types = {
 *     "koru_icons"
 *   }
 * )
 */
class KoruIconsDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $icons = \Drupal::service('koru_icons.manager')->getSelectableIcons($this->fieldDefinition);

    $attributes = [];
    foreach($icons as $class_name => $label)
    {
      $attributes[$class_name] = new Attribute(['data-class' => $class_name]);
    }

    $element['value']['#type'] = 'select_icons';
    $element['value']['#empty_value'] = '';
    $element['value']['#options'] = $icons;
    $element['value']['#options_attributes'] = $attributes;
    $element['value']['#default_value'] = (isset($items[$delta]->value) && isset($icons[$items[$delta]->value])) ? $items[$delta]->value : NULL;
    $element['value']['#description'] = t('Select an Icon');
    $element['value']['#title'] = $element['#title'];
    $element['value']['#attached']['library'][] = 'koru_icons/koru_icons.admin';

    return $element;
  }
}
