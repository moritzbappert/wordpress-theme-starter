<?php

/**
 * @file
 * Contains \TemplateTheme\Theme\Theme.
 */

namespace TemplateTheme\Theme;

class Theme {

  /**
   * Gettext localization domain.
   *
   * @var string
   */
  const L10N = 'template-theme';

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

    if (is_admin()) {
      return;
    }
  }

  /**
   * @implements init
   */
  public static function init() {
    if (is_admin()) {
      // @see Admin::init()
      return;
    }

    // Adds CSS and JS.
    add_action('wp_enqueue_scripts', __CLASS__ . '::enqueueAssets', 100);
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

    wp_enqueue_style('theme/main', static::getBaseUrl() . '/dist/styles/libs.min.css', FALSE, $git_version);
    wp_enqueue_style('theme/main', static::getBaseUrl() . '/dist/styles/main.min.css', FALSE, $git_version);
    wp_enqueue_style('theme/print', static::getBaseUrl() . '/dist/styles/print.min.css', FALSE, $git_version, 'print');

    wp_enqueue_script('theme/libs', static::getBaseUrl() . '/dist/scripts/libs.min.js', ['jquery'], $git_version, TRUE);
    wp_enqueue_script('theme/main', static::getBaseUrl() . '/dist/scripts/main.min.js', ['theme/libs'], $git_version, TRUE);
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
