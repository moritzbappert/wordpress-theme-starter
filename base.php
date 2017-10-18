<?php
namespace Starter\Theme;
?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<?php Theme::renderTemplate('templates/head'); ?>
<body <?php body_class(); ?>>
<div class="site-wrapper">
<?php
do_action('get_header');
Theme::renderTemplate('templates/header');
?>
<main class="main" role="main">
  <?php if (is_active_sidebar('sidebar-main-top')): ?>
  <div class="container container--no-padding">
    <aside class="main-top"><?php dynamic_sidebar('sidebar-main-top'); ?></aside>
  </div>
  <?php endif; ?>
  <div class="container container--flex">
    <?php include TemplateWrapper::getTemplatePath(); ?>
  </div>
  <?php if (is_active_sidebar('sidebar-main-bottom')): ?>
  <div class="container container--no-padding">
    <aside class="main-bottom"><?php dynamic_sidebar('sidebar-main-bottom'); ?></aside>
  </div>
  <?php endif; ?>
</main>
<?php
do_action('get_footer');
Theme::renderTemplate('templates/footer');
wp_footer();
?>
</div><!-- /.site-wrapper -->
</body>
</html>
