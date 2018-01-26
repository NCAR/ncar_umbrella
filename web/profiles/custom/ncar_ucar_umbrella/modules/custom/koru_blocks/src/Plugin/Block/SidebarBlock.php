<?php

namespace Drupal\koru_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuActiveTrail;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\koru_blocks\KoruMenuService;

/**
 *
 * @Block(
 *   id = "sidebar_block",
 *   admin_label = @Translation("Sidebar block"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */

class SidebarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $items = $this->buildSidebarMenu();

    if(!$items['current_page'])
    {
      return [];
    }

    return [
      '#theme' => 'sidebar_block',
      '#parent' => $items['parent'],
      '#current_page' => $items['current_page'],
      //this should invalidate cache any time the main menu is changed
      //or user changes active trail
      '#cache' => [
        'tags' => ['config:system.menu.main'],
        'contexts' => ['route.menu_active_trails:main']
      ],
    ];
  }

  protected function buildSidebarMenu()
  {
    /** @var KoruMenuService $koru_menu */
    $koru_menu = \Drupal::service('koru_menu.manager');

    return [
      'parent' => $koru_menu->getCurrentPageParentItem('main'),
      'current_page' => $koru_menu->getCurrentPageMenu('main'),
    ];

  }
}
