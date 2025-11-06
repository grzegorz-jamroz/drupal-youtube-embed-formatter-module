<?php

declare(strict_types=1);

namespace Drupal\youtube_embed_formatter\Drush\Commands;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;
use PlainDataTransformer\Transform;

final class CreateDemoArticleCommands extends DrushCommands
{
  use AutowireTrait;

  const NAME = 'demo:create-demo-article';

  private EntityStorageInterface $nodeStorage;
  private EntityStorageInterface $taxonomyTermStorage;

  public function __construct()
  {
    parent::__construct();

    $this->nodeStorage = \Drupal::entityTypeManager()->getStorage('node');
    $this->taxonomyTermStorage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  }

  /**
   * Create demo article.
   */
  #[CLI\Command(name: self::NAME, aliases: ['cda'])]
  public function createDemoArticle(): void
  {
    $nodes = array_values($this->nodeStorage->loadByProperties(['title' => 'Demo Article using YouTube Embed Formatter']));
    $node = reset($nodes);

    if ($node) {
      $this->io()->info(dt('Article with title "@title" already exists.', ['@title' => 'Demo Article using YouTube Embed Formatter']));

      return;
    }

    $tagId = $this->getTagId('demo');
    $nodeData = [
      'type' => 'article',
      'title' => 'Demo Article using YouTube Embed Formatter',
      'body' => ['value' => 'This article was created to test YouTube Embed Formatter', 'format' => 'basic_html'],
      'uid' => 1,
      'status' => 1,
      'field_youtube_video' => 'https://www.youtube.com/watch?v=EBktLwNlyVU',
    ];

    if ($tagId > 0) {
      $nodeData['field_tags'] = [['target_id' => $tagId]];
    }

    try {
      $node = Node::create($nodeData);
      $node->save();
      $this->io()->success(dt('Created article node with uuid: @uuid', ['@uuid' => $node->uuid()]));
    } catch (\Throwable $e) {
      $this->io()->error(dt('Unable to create demo article. @message', ['@message' => $e->getMessage()]));
    }
  }

  private function getTagId(string $name): int
  {
    $terms = array_values($this->taxonomyTermStorage->loadByProperties(['name' => $name]));
    $term = reset($terms);

    if ($term) {
      return Transform::toInt($term->id());
    }

    try {
      $term = Term::create(['vid' => 'tags', 'name' => $name]);
      $term->save();

      return Transform::toInt($term->id());
    } catch (\Throwable $e) {
      return 0;
    }
  }
}
