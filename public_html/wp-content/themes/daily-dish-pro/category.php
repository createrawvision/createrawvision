<?php

/**
 * For empty category archives show all sub-categories
 */
add_action('genesis_loop_else', function () {
  if (!is_admin() && is_main_query() && is_category()) {

    $parent_category = get_queried_object();

    $child_categories = get_categories(array(
      'orderby' => 'name',
      'parent' => $parent_category->term_id
    ));

    if (count($child_categories) > 0) :

      // Don't show the "no content matches" message
      remove_action('genesis_loop_else', 'genesis_do_noposts');

      foreach ($child_categories as $category) :
        $image = get_field('featured_image', $category);
        $link = esc_url(get_category_link($category->term_id));
?>
        <article class="entry">
          <header class="entry-header">
            <h2 class="entry-title">
              <a class="entry-title-link" href="<?php echo $link; ?>">
                <?php echo $category->name; ?>
              </a>
            </h2>
          </header>
          <?php if (!empty($image)) : ?>
            <div class="entry-content">
              <a class="entry-image-link" href="<?php echo $link; ?>">
                <img class="alignleft" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
              </a>
            </div>
          <?php endif; ?>
        </article>
<?php
      endforeach;

    endif;
  }
}, 9);

genesis();