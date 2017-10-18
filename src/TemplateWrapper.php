<?php

/**
 * @file
 * Contains \Starter\Theme\TemplateWrapper.
 */

namespace Starter\Theme;

class TemplateWrapper {

  /**
   * Stores the full path to the content template file.
   *
   * @var string
   */
  public static $content_template;

  /**
   * @implements template_include
   */
  public static function template_include($content_template) {
    static::$content_template = $content_template;
    $templates = [];
    if ('index.php' !== $content_template_basename = basename($content_template)) {
      $templates[] = 'base-' . $content_template_basename;
    }
    $templates[] = 'base.php';
    return locate_template(apply_filters('theme/wrap_base', $templates));
  }

  /**
   * Returns the path of the current template.
   *
   * @return string
   */
  public static function getTemplatePath() {
    return static::$content_template;
  }
}
