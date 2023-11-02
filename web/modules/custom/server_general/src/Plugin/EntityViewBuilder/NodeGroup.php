<?php

namespace Drupal\server_general\Plugin\EntityViewBuilder;

use Drupal\Core\Link;
use Drupal\media\MediaInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\server_general\ElementLayoutTrait;
use Drupal\server_general\ElementNodeNewsTrait;
use Drupal\server_general\EntityDateTrait;
use Drupal\server_general\EntityViewBuilder\NodeViewBuilderAbstract;
use Drupal\server_general\InnerElementTrait;
use Drupal\server_general\LineSeparatorTrait;
use Drupal\server_general\LinkTrait;
use Drupal\server_general\SocialShareTrait;
use Drupal\server_general\TitleAndLabelsTrait;
use Drupal\user\Entity\User;
use Drupal\og\OgMembershipInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The "Node Group" plugin.
 *
 * @EntityViewBuilder(
 *   id = "node.group",
 *   label = @Translation("Node - Group"),
 *   description = "Node view builder for Group bundle."
 * )
 */
class NodeGroup extends NodeViewBuilderAbstract {

  use ElementLayoutTrait;
  use ElementNodeNewsTrait;
  use EntityDateTrait;
  use InnerElementTrait;
  use LineSeparatorTrait;
  use LinkTrait;
  use SocialShareTrait;
  use TitleAndLabelsTrait;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $plugin->renderer = $container->get('renderer');

    return $plugin;
  }

  /**
   * Build full view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildFull(array $build, NodeInterface $entity) {

    // The node's label.
    $node_type = NodeType::load($entity->bundle());
    $label = $node_type->label();

    $user = User::load(\Drupal::currentUser()->id());
    $this->messenger()->addMessage($this->t('Hi %name, %click if you would like to subscribe to this group called %label.', [
      '%name' => $user->get('name')->value,
      '%click' => Link::createFromRoute(t('Click Here'), 'og.subscribe', ['entity_type_id' => $entity->getEntityTypeId(), 'group' => $entity->id(), 'og_membership_type' => 'default'])->toString(),
      '%label' => $entity->label(),
    ]));    

    // The hero responsive image.
    $medias = $entity->get('field_featured_image')->referencedEntities();
    $image = $this->buildEntities($medias, 'hero');

    $element = $this->buildElementNodeNews(
      $entity->label(),
      $label,
      $this->getFieldOrCreatedTimestamp($entity, 'field_publish_date'),
      $image,
      $this->buildProcessedText($entity),
      $this->buildTags($entity),
      $entity->toUrl('canonical', ['absolute' => TRUE]),
    );

    $build[] = $element;

    return $build;
  }

  /**
   * Build Teaser view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildTeaser(array $build, NodeInterface $entity) {
    $media = $this->getReferencedEntityFromField($entity, 'field_featured_image');
    $image = $media instanceof MediaInterface ? $this->buildImageStyle($media, 'card', 'field_media_image') : [];
    $title = $entity->label();
    $url = $entity->toUrl();
    $summary = $this->buildProcessedTextTrimmed($entity, 'field_body');
    $timestamp = $this->getFieldOrCreatedTimestamp($entity, 'field_publish_date');

    $element = $this->buildInnerElementWithImageForNews(
      $image,
      $title,
      $url,
      $summary,
      $timestamp
    );

    $build[] = $element;

    return $build;
  }

  /**
   * Build Teaser view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildFeatured(array $build, NodeInterface $entity) {
    $media = $this->getReferencedEntityFromField($entity, 'field_featured_image');
    $image = $media instanceof MediaInterface ? $this->buildImageStyle($media, 'card', 'field_media_image') : NULL;
    $title = $entity->label();
    $url = $entity->toUrl();
    $summary = $this->buildProcessedText($entity, 'field_body');
    $timestamp = $this->getFieldOrCreatedTimestamp($entity, 'field_publish_date');

    $element = $this->buildInnerElementWithImageHorizontalForNews(
      $image,
      $title,
      $url,
      $summary,
      $timestamp
    );

    $build[] = $element;

    return $build;
  }

  /**
   * Build "Search index" view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildSearchIndex(array $build, NodeInterface $entity) {
    $element = $this->buildInnerElementSearchResult(
      $this->t('News'),
      $entity->label(),
      $entity->toUrl(),
      $this->buildProcessedText($entity, 'field_body'),
      $this->getFieldOrCreatedTimestamp($entity, 'field_publish_date')
    );

    $build[] = $element;

    return $build;
  }

}
