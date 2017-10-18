<?php
namespace Starter\Theme;

while (have_posts()) {
  the_post();
  Theme::renderTemplate('templates/article', get_post_type());
}
?>
</div>

