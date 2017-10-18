<?php

/**
 * @file
 * Contains \Starter\Theme\Article.
 */

namespace Starter\Theme;

class Article {

  public static function init() {
    add_filter('the_content', __CLASS__ . '::the_content', 11);

    add_filter('gallerya/image_caption', __CLASS__ . '::gallerya_image_caption', 10, 2);
  }

  /**
   * @implements excerpt_length
   */
  public static function excerpt_length() {
    return 20;
  }

  /**
   * @implements excerpt_more
   */
  public static function excerpt_more() {
    return ' <a class="more" href="' . get_permalink() . '">' . __('Read more', Theme::L10N) . '</a>';
  }

  /**
   * @implements the_content
   */
  public static function the_content($content) {
    if (is_singular(['post', 'page'])) {
      // Add figcaption and credit to content images.
      $content = preg_replace_callback('@(<figure [^>]*>)?(<a [^>]*>)?(<img .*?class="([^"]*wp-(?:image|post)-([0-9]+)[^"]*)"[^>]*>)(</a>)?(<figcaption[^>]*>(.*?)</figcaption>)?(</figure>)?@', function ($matches) {
        $id = $matches[5];
        $matches[8] = !empty($matches[8]) ? $matches[8] : NULL;
        $caption = static::renderImageCaption($id, $matches[8]);
        // Remove duplicate image classes.
        $matches[3] = strtr($matches[3], [$matches[4] => '']);
        if ($matches[2] && empty($matches[6])) {
          $matches[6] = '</a>';
        }
        elseif (empty($matches[2])) {
          $matches[2] = '';
          $matches[6] = '';
        }
        return '<div class="figure-wrapper">' . ($matches[1] ?: '<figure class="' . $matches[4] . '">') . $matches[2] . $matches[3] . $matches[6] . $caption . '</figure>' . '</div>';
      }, $content);
    }
    return $content;
  }

  /**
   * Returns whether the given login name is the fallback author.
   *
   * @param string $user_name
   * @param string $fallback_name
   *
   * @return boolean
   */
  public static function isFallbackAuthor($user_name, $fallback_name = 'Redaktion') {
    return $user_name === $fallback_name;
  }

  /**
   * Returns information about the current post author.
   *
   * @return array
   */
  public static function getAuthor($id = '') {
    if (!$author['name'] = get_post_meta(get_the_ID(), 'author', TRUE)) {
      $author['name'] = get_the_author_meta('display_name', $id);
    }
    $author['prefix'] = get_post_meta(get_the_ID(), 'author_prefix', TRUE);
    $author['ID'] = get_the_author_meta('ID', $id);
    $author['user_login'] = get_the_author_meta('user_login', $id);
    $author['email'] = get_the_author_meta('user_email', $id);
    $author['job'] = get_the_author_meta('acf_position', $id);
    $author['bio'] = get_the_author_meta('description', $id);
    return $author;
  }

  /**
   * Renders a FIGCAPTION for an image attachment with caption, credit, and filename.
   *
   * @param int $attachment_id
   *   The ID of the image attachment.
   * @param string $custom_caption
   *   (optional) A custom image caption; e.g., as contained in post_body.
   *
   * @return string
   *   The rendered FIGCAPTION.
   */
  public static function renderImageCaption($attachment_id, $custom_caption = NULL, $display_filename = FALSE) {
    $caption = '';
    $credit = '';
    $subpathname = '';

    if (isset($custom_caption)) {
      $caption = trim($custom_caption);
    }
    if ($post = get_post($attachment_id)) {
      if ($caption === '') {
        $caption = trim($post->post_excerpt);
      }
      $credit = $post->credit;
      $meta = $post->_wp_attachment_metadata;
      if ($display_filename) {
        $subpathname = $meta['file'];
      }
    }

    $html = '';
    if ($caption) {
      $html .= '<span class="caption">' . $caption . '</span>';
    }
    if ($caption && $credit) {
      $html .= ' | ';
    }
    if ($credit) {
      $html .= '<span class="credit">' . 'Foto: ' . trim($credit) . '</span>';
    }
    if ($subpathname) {
      $html .= '<span class="caption-filename">' . $subpathname . '</span>';
    }
    if ($html) {
      $html = '<figcaption class="wp-caption-text caption-' . $attachment_id . '">' . $html . '</figcaption>';
    }
    return $html;
  }

  public static function gallerya_image_caption($caption, $attachment_id) {
    return static::renderImageCaption($attachment_id);
  }

}
