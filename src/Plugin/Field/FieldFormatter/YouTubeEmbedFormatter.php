<?php

declare(strict_types=1);

namespace Drupal\youtube_embed_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\youtube_embed_formatter\Service\YouTubeExtractor;
use PlainDataTransformer\Transform;

/**
 * Plugin implementation of the 'youtube_embed' formatter.
 */
#[FieldFormatter(
  id: 'youtube_embed',
  label: new TranslatableMarkup('Responsive YouTube Embed'),
  field_types: [
    'string',
    'text',
  ],
)]
class YouTubeEmbedFormatter extends FormatterBase
{
  const CONTROLS_SETTING = 'controls';
  const PRIVACY_MODE_SETTING = 'privacy_mode';

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array
  {
    return [
      ...parent::defaultSettings(),
      self::CONTROLS_SETTING => true,
      self::PRIVACY_MODE_SETTING => false,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array
  {
    $elements[self::CONTROLS_SETTING] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show player controls'),
      '#default_value' => $this->getSetting(self::CONTROLS_SETTING),
    ];

    $elements[self::PRIVACY_MODE_SETTING] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable privacy-enhanced mode'),
      '#description' => $this->t('Uses the youtube-nocookie.com domain.'),
      '#default_value' => $this->getSetting(self::PRIVACY_MODE_SETTING),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array
  {
    $settings = $this->getSettings();

    return [
      $this->t('Player controls: @value', ['@value' => $settings[self::CONTROLS_SETTING] ? 'Shown' : 'Hidden']),
      $this->t('Privacy-enhanced mode: @value', ['@value' => $settings[self::PRIVACY_MODE_SETTING] ? 'Enabled' : 'Disabled'])
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array
  {
    $elements = [];
    $settings = $this->getSettings();
    $baseUrl = $settings[self::PRIVACY_MODE_SETTING] ? 'https://www.youtube-nocookie.com/embed' : 'https://www.youtube.com/embed';

    foreach ($items as $key => $item) {
      $videoId = new YouTubeExtractor()->getVideoId($item->value);

      if ($videoId === '') {
        continue;
      }

      $iframeUrl = sprintf('%s/%s', $baseUrl, $videoId);
      $params = [];

      if (Transform::toBool($settings[self::CONTROLS_SETTING]) === false) {
        $params[self::CONTROLS_SETTING] = '0';
      }

      if ($params !== []) {
        $iframeUrl = sprintf('%s?%s', $iframeUrl, http_build_query($params));
      }

      $elements[$key] = [
        '#theme' => 'youtube_embed_formatter', // theme hook from YouTubeEmbedThemeHooks
        '#iframe_url' => $iframeUrl,
        '#video_id' => $videoId,
        '#attached' => [
          'library' => [
            'youtube_embed_formatter/embed_styles', // Attach CSS library
          ],
        ],
      ];
    }

    return $elements;
  }
}
