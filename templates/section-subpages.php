<?php
namespace Starter\Theme;

extract(Theme::getTemplateVariables() + [
  'is_page' => $post->post_type === 'page',
  'image_size' => get_post_meta($post->ID, 'image_size', TRUE),
  'is_on_own_page' => FALSE,
]);
?>

<?php
$args = [
  'post_type' => 'page',
  'post_parent' => $post->ID,
  'posts_per_page' => -1,
  'orderby' => 'menu_order',
  'order' => 'ASC',
];
$query = new \WP_Query($args);
while ($query->have_posts()):
  $query->the_post();
  $vars = [
    'image_size' => $image_size,
    'is_on_own_page' => $is_on_own_page,
  ];
  Theme::renderTemplate('templates/article', isset($content_part) ? $content_part : '', $vars);
endwhile;
wp_reset_postdata();
