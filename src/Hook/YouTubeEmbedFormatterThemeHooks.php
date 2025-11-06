<?php

declare(strict_types=1);

namespace Drupal\youtube_embed_formatter\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class YouTubeEmbedFormatterThemeHooks
{
  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array
  {
    return [
      'youtube_embed_formatter' => [
        'variables' => [
          'iframe_url' => NULL,
          'video_id' => NULL,
        ],
        'template' => 'youtube-embed-formatter',
      ],
    ];
  }
}
