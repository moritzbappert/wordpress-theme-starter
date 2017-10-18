<?php
namespace Starter\Theme;
?>
<form role="search" method="GET" class="search-form" action="<?= esc_url(home_url('/')); ?>">
  <label for="search" class="sr-only"><?= __('Search for', Theme::L10N) ?>:</label>
  <input id="search" type="search" name="s" value="<?= get_search_query(); ?>" class="search-form__field" placeholder="<?= __('Search', Theme::L10N) ?> ..." required>
  <button type="button" class="search-form__reset"><i class="icon-close"></i></button>
</form>
