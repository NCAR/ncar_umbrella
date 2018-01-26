<?php

namespace Drupal\koru_blocks;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Menu\MenuActiveTrail;

/**
 * Class KoruMenuService
 * Provides convenience methods for the over-complicated Drupal menu api
 *
 * @package Drupal\koru_blocks
 */
class KoruMenuService {

  /**
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface|null
   */
  protected $menu_tree = null;

  /**
   * @var MenuActiveTrail
   */
  protected $active_trail = null;

  /**
   * KoruMenuService constructor.
   */
  public function __construct()
  {
    $this->menu_tree = \Drupal::menuTree();
    $this->active_trail = \Drupal::service('menu.active_trail');
  }

  /**
   * Boilerplate for building a menu. You can pass in custom parameters
   * or just use the typical default set. Typically you wouldn't use this
   * but rather ::getMenuLinks
   *
   * @param $menu_name string e.g. 'main'
   * @param \Drupal\Core\Menu\MenuTreeParameters|NULL $parameters
   * @return \Drupal\Core\Menu\MenuLinkTreeElement[]
   */
  public function getMenuTree($menu_name, MenuTreeParameters $parameters = null) {

    if(!$parameters)
    {
      // Build the typical default set of menu tree parameters.
      $parameters = $this->menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
    }

    // Load the tree based on this set of parameters.
    $tree = $this->menu_tree->load($menu_name, $parameters);

    // Transform the tree using the manipulators you want.
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],// Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],// Use the default sorting of menu links.
    ];

    return $this->menu_tree->transform($tree, $manipulators);

  }

  /**
   * Convenience method to build a sane array of menu items from MenuLinkTreeElements
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeElement[] $tree
   * @return array of basic link objects [{title, url, description, children},{...}]
   * {...}]
   * @internal param \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent[] $items objects
   */
  public function getLinksFromTree(array $tree)
  {
    // a render array from the tree
    $menu_data = $this->menu_tree->build($tree);
    if (!isset($menu_data['#items']) || !$menu_data['#items']) return [];

    return $this->buildLinks($menu_data['#items']);
  }

  /**
   * @param $items array The menu items in a render array
   * @return array of basic link objects [{title, url, description, children},{...}]
   * {...}]
   */
  public function buildLinks($items)
  {
    $links = [];

    foreach($items as $item)
    {
      $links[] = $this->buildLink($item);
    }

    return $links;
  }

  /**
   * @param $item array A menu item from a render array
   * @return \stdClass representing a link {url, title, description, children}
   */
  public function buildLink($item)
  {
    $link = new \stdClass();
    $link->title = $item['title'];

    //'url' is actually a Drupal\Core\Url but it has a toString() method
    $link->url = $item['url'];

    //'original_link' is a Drupal\menu_link_content\Plugin\Menu\MenuLinkContent
    $link->description = $item['original_link']->getDescription();

    //'below' is a list of children and grandchildren so we'll recurse down
    if($item['below'])
    {
      $link->children = $this->buildLinks($item['below']);
    }
    else
    {
      $link->children = [];
    }

    return $link;
  }

  /**
   * Get menu links for a given menu and given parameters
   *
   * @param $menu string Name of the menu to use
   * @param $parameters MenuTreeParameters
   * @return array stdClass[]
   */
  public function getMenuLinks($menu, MenuTreeParameters $parameters = null)
  {
    $tree = $this->getMenuTree($menu, $parameters);
    $links = $this->getLinksFromTree($tree);

    return $links;
  }

  /**
   * Get the menu links including, and below, the current page
   *
   * @param $menu string Name of the menu current page is in
   * @return \stdClass|null
   */
  public function getCurrentPageMenu($menu)
  {
    $current_link = $this->active_trail->getActiveLink($menu);

    if(!$current_link) return null;

    $parameters = new MenuTreeParameters();
    $parameters->setRoot($current_link->getPluginId());

    $links = $this->getMenuLinks($menu, $parameters);

    return $links ? reset($links) : null;
  }

  /**
   * Get the menu item of the parent of the current page.
   * This includes the parent link and any children links,
   * but not grand children
   *
   * @param $menu string name of menu
   * @return \stdClass|null
   */
  public function getCurrentPageParentItem($menu)
  {
    $current_link = $this->active_trail->getActiveLink($menu);
    if(!$current_link) return null;

    $parent_id = $current_link->getParent();
    if($parent_id == '') return null;

    $parameters = new MenuTreeParameters();
    $parameters->setRoot($parent_id);
    $parameters->setMaxDepth(1);

    $links = $this->getMenuLinks($menu, $parameters);

    return $links ? reset($links) : null;
  }

  /**
   * Get the top level menu item in the active trail of a given menu
   * @param string $menu name of the menu
   * @return mixed|null
   */
  public function getActiveTopLevelLink($menu)
  {
    $link = null;

    //returns a list of ids in reverse order
    //last in array is home, next to last should be "top level"
    $ids = $this->active_trail->getActiveTrailIds($menu);

    if(count($ids) > 1)
    {
      $id = array_slice($ids, -2, 1);
      $top_level_id = reset($id);

      $parameters = new MenuTreeParameters();
      $parameters->setRoot($top_level_id);
      $parameters->setMaxDepth(1);

      $links = $this->getMenuLinks($menu, $parameters);

      $link = reset($links);
    }

    return $link;
  }
}
