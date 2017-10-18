<?php
namespace Starter\Theme;

extract(Theme::getTemplateVariables() + [
  'show_title' => TRUE,
]);
?>

<div class="col-left">
  <?php if (function_exists('yoast_breadcrumb') && !is_front_page()): ?>
    <nav class="breadcrumb"><?php yoast_breadcrumb(); ?></nav>
  <?php endif; ?>
  <?php if ($show_title): ?>
    <h1><?= Theme::getPageTitle(); ?></h1>
  <?php endif ?>
  <?php if (is_search()): ?>
    <form role="search" method="GET" class="search-form-result-page" action="<?= esc_url(home_url('/')) ?>">
      <label for="search" class="sr-only"><?= __('Search for:', Theme::L10N) ?></label>
      <input id="search" type="search" name="s" value="<?= get_search_query() ?>" class="search-form__field" placeholder="<?= __('Search…', Theme::L10N) ?>" required>
      <button type="submit" class="search-form__submit"><?= __('Search') ?></i></button>
    </form>
  <?php endif; ?>
  <?php if (!have_posts()): ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', Theme::L10N); ?>
  </div>
  <?php else:
    Theme::renderTemplate('templates/section', 'news');
  endif; ?>
</div>

<?php if (is_active_sidebar('sidebar-primary')): ?>
<aside class="col-right">
  <?php Theme::renderTemplate('templates/sidebar'); ?>
</aside>
<?php endif; ?>
