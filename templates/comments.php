<?php
namespace Starter\Theme;

if (post_password_required()) {
  return;
}
?>
<section id="comments" class="comments">
  <div class="container">
  <?php if (have_comments()) : ?>

    <ol class="comment-list">
      <?php wp_list_comments(['style' => 'ol', 'short_ping' => true]); ?>
    </ol>

    <?php if (get_option('page_comments') && get_comment_pages_count() > 1): ?>
      <nav>
        <ul class="pager">
          <?php if (get_previous_comments_link()): ?>
            <li class="previous"><?php previous_comments_link(__('&larr; Older comments', Theme::L10N)); ?></li>
          <?php endif; ?>
          <?php if (get_next_comments_link()): ?>
            <li class="next"><?php next_comments_link(__('Newer comments &rarr;', Theme::L10N)); ?></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; // have_comments() ?>
  <?php comment_form(); ?>
  <?php if (!comments_open() && post_type_supports(get_post_type(), 'comments')): ?>
    <h3 class="comment-closed"><?php _e('Comments are closed.', Theme::L10N); ?></h3>
  <?php endif; ?>
  </div>
</section>
