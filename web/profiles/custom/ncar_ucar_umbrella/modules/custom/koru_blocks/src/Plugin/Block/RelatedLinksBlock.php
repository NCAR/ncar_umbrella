<?php

namespace Drupal\koru_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;

/**
 *
 * @Block(
 *   id = "related_links_block",
 *   admin_label = @Translation("Related Links block"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */
class RelatedLinksBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $node = $this->getContextValue('node');

    /** @var EntityReferenceRevisionsFieldItemList $field_page_links */
    $field_page_links = $node->get('field_page_related_links');

    //don't show the block if there are no related links
    if(!$field_page_links->referencedEntities())
    {
      return [];
    }

    $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder('paragraph');

    return array(
      '#theme' => 'related_links_block',
      '#related_links' => $viewBuilder->viewField($field_page_links, ['label' => 'hidden'])
    );
  }
}
