<?php
namespace Starter\Theme;
?>

<div class="col-left">
<?php
Theme::renderTemplate('templates/article', get_post_type(), [
  'show_sidebar' => FALSE,
  'image_size' => 'post-thumbnail-cropped',
]);
?>
</div>

<?php if (is_active_sidebar('sidebar-primary')): ?>
<aside class="col-right">
  <?php Theme::renderTemplate('templates/sidebar'); ?>
</aside>
<?php endif; ?>

