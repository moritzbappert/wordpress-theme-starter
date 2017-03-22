<?php
namespace TemplateTheme\Theme;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<?php Theme::renderTemplate('templates/head'); ?>
<body <?php body_class(); ?>>
<?php
do_action('get_footer');
wp_footer();
?>
</body>
</html>
