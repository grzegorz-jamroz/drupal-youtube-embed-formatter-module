<?php

declare(strict_types=1);

namespace Drupal\youtube_embed_formatter\Service;

class YouTubeExtractor
{
  /**
   * @return string The extracted YouTube video ID, or an empty string if not found.
   */
  public function getVideoId(string $url): string
  {
    $pattern = '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/';

    if (preg_match($pattern, $url, $matches)) {
      return $matches[1];
    }

    return '';
  }
}
