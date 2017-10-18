<?php
namespace Starter\Theme;

if (!$post) {
  return;
}

extract(Theme::getTemplateVariables() + [
  'is_on_own_page' => $is_on_own_page = is_singular() && get_queried_object() instanceof \WP_Post && get_queried_object()->ID == $post->ID,
  'is_page' => $is_page = $post->post_type === 'page',
  'link' => !empty(trim($post->post_content)) && get_the_excerpt() !== apply_filters('get_the_excerpt', $post->post_content),
  'image_size' => $is_on_own_page ? 'post-thumbnail' : 'thumbnail',
  'show_title' => TRUE,
  'show_subtitle' => TRUE,
  'custom_hide_date' => $custom_hide_date = (bool) $post->custom_hide_date,
  'show_date' => !is_front_page() && !$is_on_own_page && get_post_type() === 'post' && !$custom_hide_date,
  'show_sidebar' => FALSE,
  'show_content' => FALSE,
  'show_more_link' => TRUE,
  'show_excerpt' => FALSE,
  'is_page_overview' => $is_page && $is_on_own_page && empty(trim($post->post_content)) && $child_pages = Theme::getChildMenu('hauptmenue', $post->ID),
  'hide_image_on_own_page' => (bool) $post->custom_hide_image_on_own_page,
]);

$author = Article::getAuthor();
$additional_author_1 = $post->acf_additional_author_1;
$additional_author_2 = $post->acf_additional_author_2;
?>

<?php if ($is_page_overview): ?>
  <?php Theme::renderTemplate('templates/page', 'overview', [
    'child_pages' => $child_pages,
    'post' => $post,
    'image_size' => 'page-tile',
  ]); ?>
<?php else: ?>
<article <?php (!has_post_thumbnail() || ($is_on_own_page && $hide_image_on_own_page)) ? post_class(['article', 'article--no-image']) : post_class('article'); ?>>
<?php if (has_post_thumbnail() && (!$is_on_own_page || !$hide_image_on_own_page)): ?>
  <figure class="article__image">
<?php if (!$is_on_own_page): ?>
  <?php if ($link): ?><a href="<?php the_permalink() ?>"><?php endif; ?>
    <?php the_post_thumbnail($image_size) ?>
  <?php if ($link): ?></a><?php endif; ?>
<?php elseif($is_on_own_page && !$hide_image_on_own_page): ?>
    <a href="<?= wp_get_attachment_image_src(get_post_thumbnail_id(), 'large')[0] ?>">
      <?php the_post_thumbnail($image_size) ?>
      <?php if ($custom_post_thumbnail_caption = get_post_meta($post->ID, 'custom_post_thumbnail_caption', TRUE)): ?>
        <?= Article::renderImageCaption(get_post_thumbnail_id(), $post->custom_post_thumbnail_caption) ?>
      <?php else: ?>
        <?= Article::renderImageCaption(get_post_thumbnail_id()) ?>
      <?php endif; ?>
    </a>
<?php endif; ?>
  </figure>
<?php endif; ?>
<?php if ($show_title): ?>
    <header class="article__header">
<?php if ($is_on_own_page): ?>
<?php if ($is_page): ?>
      <h1><?php the_title() ?></h1>
<?php else: ?>
    <?php if ($show_subtitle && $subtitle = \WPSubtitle::get_the_subtitle($post)): ?>
      <h2><?= $subtitle ?></h2>
    <?php endif; ?>
      <h1><?php the_title() ?></h1>
      <div class="article__meta">
        <time datetime="<?= get_post_time('c', TRUE) ?>" <?= $custom_hide_date ? 'class="invisible"' : '' ?>"><?= get_the_date(); ?></time>
          <?php if (!Article::isFallbackAuthor($author['user_login'], 'dpa') && !Article::isFallbackAuthor($author['user_login'], 'freelancer') && (empty($additional_author_1) && empty($additional_author_2))): ?>
            <?php Theme::renderTemplate('templates/author', 'box', ['author' => $author, 'compact' => TRUE]); ?>
          <?php endif; ?>
      </div>
<?php endif; ?>
<?php else: ?>
      <?php if ($show_subtitle && $subtitle = \WPSubtitle::get_the_subtitle($post)): ?>
        <h2><a href="<?php the_permalink() ?>"><?= $subtitle ?></a></h2>
      <?php endif; ?>
      <h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
      <?php if ($show_date): ?>
        <?= '<time datetime="' . get_post_time('c') . '">' . get_the_time('j. F Y') . '</time>' ?>
      <?php endif; ?>
      <?php if ($show_excerpt) : ?>
        <?php the_excerpt() ?>
      <?php endif; ?>
      <?php if ($show_more_link) : ?>
        <a class="more-link" href="<?php the_permalink() ?>">mehr</a>
      <?php endif; ?>
<?php endif; ?>
    </header>
<?php endif; ?>
<?php if (0 && $is_on_own_page && $show_sidebar): ?>
  <div class="container--flex">
  <div class="col-left">
<?php endif; ?>
<?php if ($is_on_own_page || $show_content) : ?>
  <div class="article__content">
    <div class="article__body">
      <?php if (is_active_sidebar('sidebar-article-aside')): ?>
        <aside class="article-aside">
          <?php dynamic_sidebar('sidebar-article-aside'); ?>
        </aside>
      <?php endif; ?>
      <?php $is_on_own_page ? the_content() : the_excerpt(); ?>
    </div>
  </div>
<?php endif; ?>
<?php if ($is_on_own_page && !$is_page): ?>
  <?php if (is_active_sidebar('sidebar-article-footer')): ?>
    <aside class="article-footer"><?php dynamic_sidebar('sidebar-article-footer'); ?></aside>
  <?php endif; ?>
  <?php if (!empty($additional_author_1) || !empty($additional_author_2)): ?>
    <div class="article__author--multiple">
  <?php endif; ?>
  <?php if (!Article::isFallbackAuthor($author['user_login'], 'dpa') && !Article::isFallbackAuthor($author['user_login'], 'freelancer')): ?>
    <?php
      Theme::renderTemplate('templates/author', 'box', ['author' => $author]);
      if (!empty($additional_author_1)) {
        $additional_author_1 = Article::getAuthor($additional_author_1);
        Theme::renderTemplate('templates/author', 'box', ['author' => $additional_author_1]);
      }
      if (!empty($additional_author_2)) {
        $additional_author_2 = Article::getAuthor($additional_author_2);
        Theme::renderTemplate('templates/author', 'box', ['author' => $additional_author_2]);
      }
    ?>
  <?php endif; ?>
  <?php if (!empty($additional_author_1) || !empty($additional_author_2)): ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?php if ($is_on_own_page): ?>
<footer class="article__footer">
<?php if ($is_on_own_page && !$is_page): ?>
  <span><?= __('Share:', Theme::L10N) ?></span>
  <ul class="article__sharing">
    <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(get_permalink()); ?>" target="_blank"><i class="icon-facebook"></i></a></li>
    <li><a href="https://twitter.com/intent/tweet?text=<?= urlencode(get_the_title()); ?>%20-&amp;url=<?= urlencode(get_permalink()); ?>" target="_blank"><i class="icon-twitter"></i></a></li>
    <li><a href="https://plus.google.com/share?url=<?= urlencode(get_permalink()); ?>" target="_blank"><i class="icon-google-plus"></i></a></li>
  </ul>
<?php endif; ?>
<?php if ($is_on_own_page && !$is_page && $categories = get_the_category()): ?>
  <ul class="article__categories">
  <?php
    foreach ($categories as $category):
      echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', Theme::L10N), $category->name)) . '">' . esc_html($category->name) . '</a></li>';
    endforeach;
  ?>
  </ul>
<?php endif; ?>
</footer>
<?php endif; ?>
<?php if ($is_on_own_page && !$is_page): ?>
  <?php do_action('related-posts', get_the_id()); ?>
<?php endif; ?>
<?php if ($is_on_own_page && !$is_page && function_exists('yoast_breadcrumb')): ?>
  <nav class="breadcrumb"><?php yoast_breadcrumb(); ?></nav>
<?php endif; ?>
<?php if (0 && $is_on_own_page && $show_sidebar): ?>
  </div>
  <?php if (is_active_sidebar('sidebar-primary')): ?>
  <aside class="col-right">
    <?php Theme::renderTemplate('templates/sidebar'); ?>
  </aside>
  </div>
  <?php endif; ?>
<?php endif ?>
</article>
<?php endif; ?>
