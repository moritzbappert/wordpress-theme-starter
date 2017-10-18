<?php
namespace Starter\Theme;
?>
<article class="article article--no-image">
  <header class="article__header">
    <h1><?= __('Page not found') ?></h1>
  </header>
  <div class="article__content">
    <div class="article__body">
      <p>
        Die aufgerufene Seite existiert nicht oder wurde gelöscht.<br>
        Wenn Sie etwas Bestimmtes suchen nutzen Sie unsere Suche:
      </p>
      <form role="search" method="GET" class="search-form-result-page" action="<?= esc_url(home_url('/')) ?>">
        <label for="search" class="sr-only"><?= __('Search for:', Theme::L10N) ?></label>
        <input id="search" type="search" name="s" value="<?= urldecode(basename($_SERVER['REQUEST_URI'])) ?>" class="search-form__field" placeholder="<?= __('Search…', Theme::L10N) ?>" required>
        <button type="submit" class="search-form__submit"><?= __('Search') ?></button>
      </form>
    </div>
  </div>
</article>
