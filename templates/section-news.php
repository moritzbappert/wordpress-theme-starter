<?php
namespace Starter\Theme;
?>

<section class="section section--news">
  <?php if($category_description = category_description()): ?>
    <div class="category__description"><?= $category_description ?></div>
  <?php endif; ?>
  <?php
  $i = 1;
  while (have_posts()) {
    the_post();
    Theme::renderTemplate('templates/article', get_post_type() !== 'post' ? get_post_type() : get_post_format());
    // Add an ad slot after the 1st and 4th post.
    if ($i === 1 || $i === 5) {
      do_action('dfp_adslot', 'superbanner', TRUE);
    }
    $i++;
  }
  wp_reset_postdata();
  the_posts_navigation();
  ?>
</section>
