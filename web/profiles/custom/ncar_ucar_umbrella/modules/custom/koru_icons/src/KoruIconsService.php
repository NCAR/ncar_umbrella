<?php

namespace Drupal\koru_icons;

use Drupal\Core\Field\FieldDefinitionInterface;

class KoruIconsService
{
  /**
   * Get array of selectable icons.
   *
   * If some icons have been selected at the default field settings, allow
   * only those to be selectable. Else, check if any have been selected for the
   * field instance. If none, allow all available icons.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *  The field definition object.
   *
   * @return array
   *   The array of icon names keyed by their css class name.
   */
  public function getSelectableIcons(FieldDefinitionInterface $field_definition) {
    $field_definition_icons = $field_definition->getSetting('selectable_icons');
    $field_storage_icons = $field_definition->getFieldStorageDefinition()->getSetting('selectable_icons');

    $icons = $this->getIcons();

    $allowed = (!empty($field_definition_icons)) ? $field_definition_icons : $field_storage_icons;
    return  (!empty($allowed)) ? array_intersect_key($icons, $allowed) : $icons;
  }
  
  public function getIcons()
  {
    $icons = [];

    $svg_file = drupal_get_path('theme', 'koru') . '/libraries/koru-base/fonts/icomoon/fonts/icomoon.svg';
    $xml = file_get_contents($svg_file);
    preg_match_all('@glyph-name="(.*?)"@', $xml, $matches);

    if(!$matches[1]) return $icons;

    foreach($matches[1] as $glyph_name)
    {
      $key = 'icon-' . $glyph_name;
      $label = ucwords(trim(str_replace(['icon', '-'], ['', ' '], $key)));
      $icons[$key] = str_replace(['Ncar', 'Ucar', 'Nsf', 'Ucp'], ['NCAR', 'UCAR', 'NSF', 'UCP'], $label);
    }

    //these aren't actually icons, don't show them
    unset($icons['icon-accronym-ncar']);
    unset($icons['icon-accronym-ucar']);
    unset($icons['icon-accronym-ncar-ucar']);

    asort($icons);

    return $icons;
  }
}