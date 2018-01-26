<?php

namespace Drupal\koru_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\koru_blocks\KoruMenuService;

/**
 *
 * @Block(
 *   id = "header_block",
 *   admin_label = @Translation("Header block"),
 * )
 */

class HeaderBlock extends BlockBase {

  protected $main_menu_name = 'main';

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('system.site');

    return array (
      '#theme' => 'header_block',
      '#main_menu_items' => $this->buildMainMenu(),
      //this should invalidate cache any time the main menu is changed
      //or user changes active trail
      '#cache' => [
        'tags' => ['config:system.menu.' . $this->main_menu_name],
        'contexts' => ['route.menu_active_trails:' . $this->main_menu_name]
      ],
      '#site_name' => $config->get('name'),
      '#site_slogan' => $config->get('slogan'),
      '#alert_exists' => $this->alertExists()
    );
  }

  /**
   * Get the links for our custom main menu
   * @return array
   */
  protected function buildMainMenu()
  {
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(2);

    /** @var KoruMenuService $koru_menu */
    $koru_menu = \Drupal::service('koru_menu.manager');
    $top_link = $koru_menu->getActiveTopLevelLink($this->main_menu_name);
    $main_menu = $koru_menu->getMenuLinks($this->main_menu_name, $parameters);
    foreach($main_menu as $link)
    {
      $link->is_active_section = ($top_link && $link->url == $top_link->url);
    }

    return $main_menu;
  }

  /**
   * Whether or not there is a current UCAR Alert
   * @return bool
   */
  protected function alertExists() {

    return (bool) \Drupal::hasService('ucar_alert_api.manager') &&
      \Drupal::service('ucar_alert_api.manager')->getAlert();

  }
}
