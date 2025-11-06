<?php

declare(strict_types=1);

namespace Drupal\youtube_embed_formatter\Drush\Commands;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;

final class AddYoutubeVideoFieldToArticleCommands extends DrushCommands
{
  use AutowireTrait;

  const NAME = 'demo:add-youtube-video-field-to-article';

  /**
   * Add YouTube Video field to article.
   */
  #[CLI\Command(name: self::NAME, aliases: ['ayvfta'])]
  public function addYoutubeVideoFieldToArticle(): void
  {
    if (!FieldStorageConfig::loadByName('node', 'field_youtube_video')) {
      FieldStorageConfig::create([
        'field_name' => 'field_youtube_video',
        'entity_type' => 'node',
        'type' => 'string',
        'cardinality' => 1,
        'translatable' => true,
        'locked' => false,
        'settings' => [
          'max_length' => 255,
          'case_sensitive' => false,
          'is_ascii' => false,
        ],
      ])->save();
    }

    if (!FieldConfig::loadByName('node', 'article', 'field_youtube_video')) {
      FieldConfig::create([
        'field_name' => 'field_youtube_video',
        'entity_type' => 'node',
        'bundle' => 'article',
        'label' => 'Youtube Video',
        'description' => 'Add YouTube url or Video ID',
        'required' => false,
        'translatable' => false,
        'default_value' => [],
        'settings' => [],
      ])->save();
    }

    $form_display = EntityFormDisplay::load('node.article.default');

    if ($form_display) {
      $form_display->setComponent('field_youtube_video', [
        'type' => 'string_textfield',
        'weight' => 121,
        'settings' => [
          'size' => 60,
          'placeholder' => '',
        ],
      ])->save();
    }

    $view_display = EntityViewDisplay::load('node.article.default');

    if ($view_display) {
      $view_display->setComponent('field_youtube_video', [
        'type' => 'youtube_embed',
        'label' => 'above',
        'weight' => 111,
        'settings' => [
          'controls' => '1',
          'privacy_mode' => 0,
        ],
        'third_party_settings' => [],
      ])->save();
    }
  }
}
