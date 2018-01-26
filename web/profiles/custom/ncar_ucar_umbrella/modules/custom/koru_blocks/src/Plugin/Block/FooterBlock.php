<?php

namespace Drupal\koru_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *   id = "footer_block",
 *   admin_label = @Translation("Footer block"),
 * )
 */

class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array (
      '#theme' => 'footer_block',
    );
  }
}
