<?php

/**
 * @file
 * Contains \TemplateTheme\Theme\Admin.
 */

namespace TemplateTheme\Theme;

class Admin {

  public static function init() {
    // Adds CSS to backend.
    add_action('admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts');

    // Adds all necessary CSS affecting article content.
    add_editor_style([
      'dist/styles/editor-style.min.css',
    ]);
  }

  /**
   * @implements admin_enqueue_scripts
   */
  public static function admin_enqueue_scripts() {
    wp_enqueue_style('theme/admin', Theme::getBaseUrl() . '/dist/styles/admin.min.css', FALSE);
  }

}
