<?php

namespace Drupal\koru_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *   id = "components_block",
 *   admin_label = @Translation("Component block"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */

class ComponentsBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->getContextValue('node');
    $field_page_components = $node->get('field_page_components');
    $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder('node');

    return array(
      '#theme' => 'components_block',
      '#components' => $viewBuilder->viewField($field_page_components, ['label' => 'hidden'])
    );
  }
}