<?php

namespace Starter\Theme;

if (!defined('ABSPATH')) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
  exit;
}

/**
 * Loads PSR-4-style classes.
 */
function classloader($class) {
  static $ns_offset;
  if (strpos($class, __NAMESPACE__ . '\\') === 0) {
    if ($ns_offset === NULL) {
      $ns_offset = strlen(__NAMESPACE__) + 1;
    }
    include __DIR__ . '/src/' . strtr(substr($class, $ns_offset), '\\', '/') . '.php';
  }
}
spl_autoload_register(__NAMESPACE__ . '\classloader');

// Another plugin hooks into after_setup_theme priority 10 so some filters
// not apply correctly - e.g. the WooCommerce image size filters.
add_action('after_setup_theme', __NAMESPACE__ . '\Theme::after_setup_theme', 9);
add_action('init', __NAMESPACE__ . '\Theme::init');
add_action('admin_init', __NAMESPACE__ . '\Admin::init');
