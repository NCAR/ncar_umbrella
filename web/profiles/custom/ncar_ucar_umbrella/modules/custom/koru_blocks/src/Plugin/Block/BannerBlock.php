<?php

namespace Drupal\koru_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *   id = "banner_block",
 *   admin_label = @Translation("Banner block"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */

class BannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->getContextValue('node');
    $title = $node->get('title')->getString();
    $subtitle = $node->get('field_page_subtitle')->getString();

    if ($node->get('field_page_banner_image')->isEmpty()) {
      $bannerImageUri = '';
    }
    else {
      $bannerImageUri = file_create_url($node->get('field_page_banner_image')->entity->getFileUri());
    }

    return array(
      '#theme' => 'banner_block',
      '#title' => $title,
      '#subtitle' => $subtitle,
      '#banner_image' => $bannerImageUri,
    );
  }
}
