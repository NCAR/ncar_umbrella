<?php

/**
 * @file
 * Contains \Drupal\koru_icons\Plugin\Field\FieldType\KoruIconsItem.
 */

namespace Drupal\koru_icons\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'koru_icons' field type.
 *
 * @FieldType(
 *   id = "koru_icons",
 *   label = @Translation("Koru Icons"),
 *   description = @Translation("Stores Icomoon Icon class name."),
 *   default_widget = "koru_icons_default",
 *   default_formatter = "koru_icons_default"
 * )
 */

class KoruIconsItem extends FieldItemBase {

  const KORU_ICONS_MAXLENGTH = 255;

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Koru Icons'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'char',
          'length' => static::KORU_ICONS_MAXLENGTH,
          'not null' => FALSE,
        ),
      ),
      'indexes' => array(
        'value' => array('value'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $constraints[] = $constraint_manager->create('ComplexData', array(
      'value' => array(
        'Length' => array(
          'max' => static::KORU_ICONS_MAXLENGTH,
          'maxMessage' => t('%name: the icon class name may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => static::KORU_ICONS_MAXLENGTH)),
        )
      ),
    ));

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return array(
      'selectable_icons' => array(),
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
        'selectable_icons' => array(),
      ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = array();
    $settings = $this->getSettings();
    // Add selectable_icons element.
    static::defaultIconsForm($element, $settings);
    $element['selectable_icons']['#description'] = t("If no icons are selected, all of them will be available for this field and will override the field's default selectable icons.");

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = array();
    // We need the field-level 'selectable_icons' setting, and $this->getSettings()
    // will only provide the instance-level one, so we need to explicitly fetch
    // the field.
    $settings = $this->getFieldDefinition()->getFieldStorageDefinition()->getSettings();
    static::defaultIconsForm($element, $settings);
    $element['selectable_icons']['#title'] = t('Available Icons');
    $element['selectable_icons']['#disabled'] = 'disabled';
    $element['selectable_icons']['#description'] = '';

    return $element;
  }

  /**
   * Builds the selectable_icons element.
   *
   * @param array $element
   *   The form associative array passed by reference.
   * @param array $settings
   *   The field settings array.
   */
  protected function defaultIconsForm(array &$element, array $settings) {
    $element['selectable_icons'] = array(
      '#type' => 'select',
      '#title' => t('Selectable icons'),
      '#default_value' => $settings['selectable_icons'],
      '#options' => \Drupal::service('koru_icons.manager')->getIcons(),
      '#description' => t('Select all icons you want to make available for this field.'),
      '#multiple' => TRUE,
      '#size' => 10,
    );
  }
}
