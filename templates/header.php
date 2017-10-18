<?php
namespace Starter\Theme;

$logo_path = Theme::getBasePath() . '/dist/images';
$logo_url = Theme::getBaseUrl() . '/dist/images/logo.svg';
if (file_exists($logo_path . '/logo.' . SKIN . '.svg')) {
  $logo_url = Theme::getBaseUrl() . '/dist/images/logo.' . SKIN . '.svg';
}
?>
<header class="site-header" role="banner">
  <a class="site-logo" href="<?= esc_url(home_url('/')) ?>">
    <img src="<?= $logo_url ?>" alt="<?php bloginfo('name'); ?>">
  </a>
  <?php if (has_nav_menu('primary')): ?>
    <button class="nav-button">
      <span></span>
      <span></span>
      <span></span>
    </button>
  <?php endif; ?>
  <?php if (has_nav_menu('secondary')): ?>
    <?php wp_nav_menu(['theme_location' => 'secondary', 'container' => 'nav', 'container_class' => 'nav-secondary move-out']); ?>
  <?php endif; ?>
  <form role="search" method="GET" class="search-form move-out js-header-search" action="<?= esc_url(home_url('/')) ?>">
    <div class="container">
      <label for="search" class="sr-only"><?= __('Search for:', Theme::L10N) ?></label>
      <button type="submit" class="search-form__submit"><i class="icon-search"></i></button>
      <input id="search" type="search" name="s" value="<?= get_search_query() ?>" class="search-form__field" placeholder="<?= __('Search…', Theme::L10N) ?>" required>
      <button type="button" class="search-form__reset"><i class="icon-close"></i></button>
    </div>
  </form>
  <?php if (has_nav_menu('primary')): ?>
    <?php wp_nav_menu(['theme_location' => 'primary', 'container' => 'nav', 'container_class' => 'nav-primary move-out', 'depth' => 3]); ?>
  <?php endif; ?>
  <ul class="social-buttons move-out">
    <li><a href="https://www.facebook.com/netzstrategen/" target="_blank"><i class="icon-facebook"></i></a></li>
    <li><a href="https://twitter.com/netzstrategen" target="_blank"><i class="icon-twitter"></i></a></li>
    <li><a href="https://www.youtube.com/channel/UCfGJBVuycuHeTKuNKrsJtGg" target="_blank"><i class="icon-youtube"></i></a></li>
    <li><a href="https://www.instagram.com/netzstrategen/" target="_blank"><i class="icon-instagram" target="_blank"></i></a></li>
  </ul>
</header>
