<?php
namespace Starter\Theme;
$author = Article::getAuthor();
?>

<div class="col-right">
  <?php Theme::renderTemplate('templates/author', 'box', [
    'author' => $author,
    'compact' => TRUE,
    'show_email' => TRUE,
    'show_bio' => empty(trim($author['bio'])) ? FALSE : TRUE
  ]); ?>
</div>
<div class="col-left">
  <?php if (!have_posts()): ?>
    <div class="alert alert-warning">
      <?php _e('Sorry, no results were found.', Theme::L10N); ?>
    </div>

  <?php else: ?>

  <?php
  while (have_posts()) {
    the_post();
    Theme::setTemplateVariables(['show_excerpt' => FALSE]);
    get_template_part('templates/article', get_post_type() !== 'post' ? get_post_type() : get_post_format());
  }
  the_posts_navigation();
  ?>

  <?php endif; ?>
</div>
