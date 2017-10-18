<?php

/**
 * @file
 * Contains \Starter\Theme\Admin.
 */

namespace Starter\Theme;

class Admin {

  public static function init() {
    // Add a class to HTML element; parent theme uses nested element styles for
    // main article content formatting.
    add_action('wp_tiny_mce_init', __CLASS__ . '::wp_tiny_mce_init');

    // Add editor to excerpt field.
    add_action('add_meta_boxes', __CLASS__ . '::add_meta_boxes');

    // Remove unwanted default buttons.
    add_filter('mce_buttons', __CLASS__ . '::mce_buttons', 10, 2);
    add_filter('mce_buttons_2', __CLASS__ . '::mce_buttons_2', 10, 2);

    // Customize editor UI.
    add_filter('tiny_mce_before_init', __CLASS__ . '::tiny_mce_before_init');

    // Add all necessary CSS affecting article content.
    add_editor_style([
      'dist/styles/editor-style.css',
    ]);

    // Expose post-thumbnail and post-thumbnail-uncropped image sizes in image
    // style options when embedding an image.
    add_filter('image_size_names_choose', __CLASS__ . '::image_size_names_choose');
  }

  /**
   * @implements add_meta_boxes
   */
  public static function add_meta_boxes() {
    global $wp_meta_boxes;

    foreach ($wp_meta_boxes as $post_type => $info) {
      if (post_type_supports($post_type, 'excerpt')) {
        $wp_meta_boxes[$post_type]['normal']['core']['postexcerpt']['callback'] =  __CLASS__ . '::post_excerpt_meta_box';
      }
    }
  }

  /**
   * @implements add_meta_box
   */
  public static function post_excerpt_meta_box($post) {
    wp_editor(html_entity_decode(stripcslashes($post->post_excerpt)), 'excerpt', [
      'textarea_rows' => '12',
      'quicktags' => TRUE,
      'tinymce' => TRUE,
      'media_buttons' => FALSE,
    ]);
  }

  /**
   * @implements wp_tiny_mce_init
   */
  public static function wp_tiny_mce_init() {
    $html_class = 'container';
    echo <<<EOD
<script>
for (id in tinyMCEPreInit.mceInit) {
  tinyMCEPreInit.mceInit[id].setup = function (editor) {
    editor.on('PreInit', function (e) {
      editor.dom.addClass(editor.dom.select('html'), '$html_class');
    });
  }
}
</script>
EOD;
  }

  /**
   * @implements mce_buttons
   */
  public static function mce_buttons(array $buttons, $editor_id) {
    // Only remove unwanted default buttons here.
    if ($editor_id === 'excerpt') {
      $buttons = ['bold', 'italic', 'link', 'unlink', '|', 'pastetext', 'removeformat', 'charmap'];
    }
    else {
      $buttons = array_diff($buttons, ['alignleft', 'aligncenter', 'alignright', 'wp_adv']);
    }
    return $buttons;
  }

  /**
   * @implements mce_buttons_2
   */
  public static function mce_buttons_2(array $buttons, $editor_id) {
    if ($editor_id === 'excerpt') {
      $buttons = [];
    }
    else {
      // Only remove unwanted default buttons here.
      $buttons = array_diff($buttons, ['underline', 'alignjustify', 'forecolor', 'outdent', 'indent', 'undo', 'redo', 'fullscreen']);
    }
    return $buttons;
  }

  /**
   * @implements tiny_mce_before_init
   */
  public static function tiny_mce_before_init(array $settings) {
    // @see Admin::wp_tiny_mce_init()
    $settings['body_class'] = 'article' . ' ' . $settings['body_class'];

    // Reduce block-formatselect options to sensible/necessary formats.
    $settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Preformatted=pre';

    // Prepare toolbar buttons for customizations.
    // This may look unnecessarily complex, but the goal is to retain any
    // additional buttons that might have been added by plugins. Unwanted default
    // buttons of WordPress Core have been removed already.
    // @see Admin::mce_buttons()
    $settings['toolbar1'] = explode(',', $settings['toolbar1']);
    array_unshift($settings['toolbar1'], 'styleselect');
    $settings['toolbar2'] = explode(',', $settings['toolbar2']);
    $buttons = array_merge($settings['toolbar1'], ['|'], $settings['toolbar2']);
    $buttons = array_diff($buttons, ['blockquote']);

    $style_formats = [
      ['title' => 'Vorspann', 'block' => 'p', 'classes' => 'intro'],
      ['title' => 'Infobox', 'block' => 'p', 'classes' => 'infobox'],
      ['title' => 'Highlight', 'block' => 'p', 'classes' => 'highlight'],
      ['title' => 'Zitat', 'items' => [
        ['title' => 'Zitat', 'block' => 'blockquote', 'wrapper' => TRUE],
        ['title' => 'Quelle', 'block' => 'cite'],
      ]],
      ['title' => 'Interview', 'items' => [
        ['title' => 'Frage', 'block' => 'p', 'classes' => 'interview__question'],
        ['title' => 'Antwort', 'block' => 'p', 'classes' => 'interview__answer'],
      ]],
    ];
    $settings['style_formats'] = json_encode($style_formats);

    // Move block-formatselect from toolbar2 to beginning.
    if (FALSE !== $index = array_search('formatselect', $buttons)) {
      unset($buttons[$index]);
      array_unshift($buttons, 'formatselect');
    }

    // Apply customized toolbar.
    $settings['toolbar1'] = implode(',', $buttons);
    $settings['toolbar2'] = '';

    // Fix old-style dividers from TinyMCE 3.x.
    $settings['toolbar1'] = str_replace(',separator,', ',|,', $settings['toolbar1']);

    return $settings;
  }

  /**
   * @implements image_size_names_choose
   */
  public static function image_size_names_choose($sizes) {
    $newsizes = [];
    $is_inserted = FALSE;
    foreach ($sizes as $size => $name) {
      if (!$is_inserted && ($size === 'large' || $size === 'full')) {
        $newsizes['post-thumbnail'] = __('Post Thumbnail');
        $newsizes['post-thumbnail-uncropped'] = __('Post Thumbnail') . ' ' . __('(uncropped)', Theme::L10N);
        $is_inserted = TRUE;
      }
      $newsizes[$size] = $name;
    }
    return $newsizes;
  }

}
