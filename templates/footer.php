<?php
namespace Starter\Theme;
?>

<div class="site-wrapper">
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="site-footer__copyright"><span>netzstrategen GmbH</span></div>
    <?php
      if (has_nav_menu('footer-left')):
        wp_nav_menu(['theme_location' => 'footer-left', 'container' => '', 'menu_class' => 'site-footer__nav']);
      endif;
      if (has_nav_menu('footer-right')):
        wp_nav_menu(['theme_location' => 'footer-right', 'container' => '', 'menu_class' => 'site-footer__nav']);
      endif;
    ?>
  </div>
</footer>
</div>
