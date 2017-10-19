<?php
namespace Bnn\Theme;

extract(Theme::getTemplateVariables() + []);
?>
<section class="page-overview <?= $post->post_name; ?>">
<?php if (is_active_sidebar('sidebar-page-overview')): ?>
  <?php dynamic_sidebar('sidebar-page-overview'); ?>
<?php endif; ?>
<?php foreach ($child_pages as $child):
  $child = (array) $child;
  $args = [
    'posts_per_page' => 1,
    'post_type' => 'any',
  ];
  if ($child['type'] === 'post_type') {
    $args['p'] = $child['object_id'];
    $custom_posts = get_posts($args);
    if ($post = reset($custom_posts)) {
      setup_postdata($post);
      $thumbnail = get_the_post_thumbnail(NULL, $image_size);
      $permalink = trim($post->link_href) ? trim($post->link_href) : get_the_permalink();
      $excerpt = get_the_excerpt();
      $title = get_the_title();
    }
    else {
      continue;
    }
  }
  elseif ($child['type'] === 'taxonomy' && $term = get_term($child['object_id'], $child['object'])) {
    $thumbnail = wp_get_attachment_image(get_field('acf_image', $term), $image_size);
    $permalink = get_term_link($term, $child['object']);
    $excerpt = apply_filters('the_content', $term->description);
    $title = $term->name;
    if ($term->count >= 1) {
      $args['cat'] = $child['object_id'];
      $custom_posts = get_posts($args);
      $post = $custom_posts[0];
      setup_postdata($post);
      if (!$excerpt || !$thumbnail) {
        $thumbnail = get_the_post_thumbnail(NULL, $image_size);
        $excerpt = get_the_excerpt();
        $title = get_the_title();
      }
      if ($term->count === 1) {
        $permalink = trim($post->link_href) ? trim($post->link_href) : get_the_permalink();
      }
    }
  }
  else {
    continue;
  }
?>
  <article <?php !$thumbnail ? post_class(['article', 'article--no-image']) : post_class('article'); ?>>
    <?php if ($thumbnail): ?>
      <figure class="article__image">
        <a href="<?= $permalink ?>"><?= $thumbnail ?></a>
      </figure>
    <?php endif; ?>
    <div class="article__content">
      <div class="article__body">
        <header class="article__header">
          <h1><a href="<?= $permalink ?>"><?= $title ?></a></h1>
        </header>
        <div class="article__excerpt">
          <?= $excerpt ?>
        </div>
        <?php if ($link_text = get_field('acf_overview_link_text')): ?>
          <a href="<?= $permalink ?>" class="btn btn--primary"><?= $link_text ?></a>
        <?php endif; ?>
      </div>
    </div>
  </article>
<?php endforeach; wp_reset_postdata(); ?>
</section>
