<?php

/**
 * @file
 * Contains \Starter\Theme\Theme.
 */

namespace Starter\Theme;

class Theme {

  /**
   * Gettext localization domain.
   *
   * @var string
   */
  const L10N = 'starter';

  /**
   * @var array
   */
  private static $templateVariables = [];

  /**
   * @var array
   */
  private static $baseUrl;

  /**
   * @implements after_setup_theme
   */
  public static function after_setup_theme() {
    load_theme_textdomain(static::L10N, static::getBasePath() . '/languages');

    // Enable excerpt for pages.
    //add_post_type_support('page', 'excerpt');

    // Add HTML5 markup for captions
    // @see http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    // Enable plugins to manage the document title.
    // @see http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
    add_theme_support('title-tag');

    // Register post thumbnail and derivative image sizes.
    add_theme_support('post-thumbnails');
    // 'post-thumbnail'
    set_post_thumbnail_size(860, 0, FALSE);
    // 'large'
    add_filter('pre_option_large_size_w', function () { return 1200; });
    add_filter('pre_option_large_size_h', function () { return 0; });
    add_filter('pre_option_large_crop', function () { return TRUE; });
    // 'medium'
    add_filter('pre_option_medium_size_w', function () { return 620; });
    add_filter('pre_option_medium_size_h', function () { return 340; });
    add_filter('pre_option_medium_crop', function () { return TRUE; });
    // 'thumbnail'
    add_filter('pre_option_thumbnail_size_w', function () { return 400; });
    add_filter('pre_option_thumbnail_size_h', function () { return 220; });
    add_filter('pre_option_thumbnail_crop', function () { return TRUE; });

    // Add post formats.
    // @see http://codex.wordpress.org/Post_Formats
    add_theme_support('post-formats', ['gallery', 'video']);

    // Register navigation menus for wp_nav_menu().
    // @see http://codex.wordpress.org/Function_Reference/register_nav_menus
    register_nav_menus([
      'primary' => __('Primary Navigation', Theme::L10N),
      'secondary' => __('Secondary Navigation', Theme::L10N),
      'footer-left' => __('Footer Left', Theme::L10N),
      'footer-right' => __('Footer Right', Theme::L10N),
    ]);

    // Declare WooCommerce support.
    add_theme_support('woocommerce');

    if (is_admin()) {
      return;
    }
  }

  /**
   * @implements widgets_init
   */
  public static function widgets_init() {
    register_sidebar([
      'id'            => 'sidebar-primary',
      'name'          => __('Primary', Theme::L10N),
      'before_widget' => '<section class="widget %1$s %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3>',
      'after_title'   => '</h3>',
    ]);
    register_sidebar([
      'id'            => 'sidebar-main-top',
      'name'          => __('Main (Top)', Theme::L10N),
      'before_widget' => '<section class="widget %1$s %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3>',
      'after_title'   => '</h3>',
    ]);
    register_sidebar([
      'id'            => 'sidebar-main-bottom',
      'name'          => __('Main (Bottom)', Theme::L10N),
      'before_widget' => '<section class="widget %1$s %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3>',
      'after_title'   => '</h3>',
    ]);
  }

  /**
   * @implements init
   */
  public static function init() {
    remove_post_type_support('page', 'wps_subtitle');

    if (is_admin()) {
      // @see Admin::init()
      return;
    }
    add_filter('template_include', __NAMESPACE__ . '\TemplateWrapper::template_include');

    add_action('wp_enqueue_scripts', __CLASS__ . '::enqueueAssets', 100);

    add_filter('body_class', __CLASS__ . '::body_class');

    // Article hooks.
    //add_filter('excerpt_more', __NAMESPACE__ . '\Article::excerpt_more');
    add_filter('excerpt_more', '__return_null');
    add_filter('excerpt_length', __NAMESPACE__ . '\Article::excerpt_length');

    // Remove limit for comments per page.
    add_filter('pre_option_page_comments', function () { return 0; });

    // Allow shortcodes on category description
    add_filter('category_description', 'do_shortcode');
  }

  /**
   * Enqueues styles and scripts.
   *
   * @implements wp_enqueue_scripts
   */
  public static function enqueueAssets() {
    $git_version = NULL;
    if (is_dir(ABSPATH . '.git')) {
      $ref = trim(file_get_contents(ABSPATH . '.git/HEAD'));
      if (strpos($ref, 'ref:') === 0) {
        $ref = substr($ref, 5);
        if (file_exists(ABSPATH . '.git/' . $ref)) {
          $ref = trim(file_get_contents(ABSPATH . '.git/' . $ref));
        }
        else {
          $ref = substr($ref, 11);
        }
      }
      $git_version = substr($ref, 0, 8);
    }

    wp_enqueue_style('theme/main', static::getBaseUrl() . '/dist/styles/main.css', FALSE, $git_version);
    wp_enqueue_style('theme/print', static::getBaseUrl() . '/dist/styles/print.css', FALSE, $git_version, 'print');

    wp_enqueue_script('theme/libs', static::getBaseUrl() . '/dist/scripts/libs.js', ['jquery'], $git_version, TRUE);
    wp_enqueue_script('theme/main', static::getBaseUrl() . '/dist/scripts/main.js', ['theme/libs'], $git_version, TRUE);

    wp_localize_script('theme/main', 'screenReaderText', array(
      'expand'   => __('Expand child menu', static::L10N),
      'collapse' => __('Collapse child menu', static::L10N),
    ));
  }

  /**
   * Adds BODY element classes.
   *
   * Extends body class by the name of the parent post slug; if WPML is active,
   * the slug of the default language.
   *
   * @implements body_class
   */
  public static function body_class(array $classes) {
    global $post;

    // Add specific body classes to specific pages.
    if (is_search()) {
      $classes[] = 'archive';
    }
    // Add slug of parent page (in default language, if any).
    if ($post) {
      $post_id = $post->post_parent ? $post->post_parent : $post->ID;
      if (function_exists('icl_object_id')) {
        $post_id = icl_object_id($post_id, 'page', FALSE, $sitepress->get_default_language());
      }
      $page = get_post($post_id);
      $classes[] = $page->post_name;
    }
    return $classes;
  }

  /**
   * Returns the title for the current (index) page.
   *
   * @return string
   */
  public static function getPageTitle() {
    if (is_home()) {
      if ($id = get_option('page_for_posts', FALSE)) {
        return get_the_title($id);
      }
      return __('Latest Posts', Theme::L10N);
    }
    elseif (is_archive()) {
      return single_cat_title( '', false );
    }
    elseif (is_search()) {
      return sprintf(__('Search Results for &ldquo;%s&rdquo;', Theme::L10N), get_search_query());
    }
    else {
      return get_the_title();
    }
  }

  /**
   * Sets additional template variables before rendering.
   *
   * @param array $variables
   *   An associative array whose keys are variables names and whose values are
   *   the corresponding values.
   */
  public static function setTemplateVariables(array $variables) {
    static::$templateVariables = $variables;
  }

  /**
   * Gets additional template variables and resets the storage.
   *
   * @return array
   *   Variables are keyed by their names, so the result may be passed to
   *   extract(). The calling code should use array_replace() to merge the stored
   *   variables into default values of the template, since variables may be
   *   omitted by rendering code.
   */
  public static function getTemplateVariables() {
    $variables = static::$templateVariables;
    static::$templateVariables = [];
    return $variables;
  }

  /**
   * Gets template part with additional template variables.
   *
   * @param mixed $file
   * @param string $name
   * @param array $variables
   *
   * @see setTemplateVariables()
   * @see get_template_part()
   */
  public static function renderTemplate($file, $name = NULL, array $variables = []) {
    static::setTemplateVariables($variables);
    get_template_part($file, $name);
  }

  /**
   * Returns the base URL of this theme.
   *
   * @return string
   */
  public static function getBaseUrl() {
    if (!isset(self::$baseUrl)) {
      self::$baseUrl = get_stylesheet_directory_uri();
    }
    return self::$baseUrl;
  }

  /**
   * The absolute filesystem base path of this theme.
   *
   * @return string
   */
  public static function getBasePath() {
    return dirname(__DIR__);
  }

}
