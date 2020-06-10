<?php

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script('crv-recipe-filter', CHILD_URL . '/js/recipe-filter.js', ['jquery'], '1.0.0', true);
});

add_filter('the_content', 'crv_recipe_filter_form');

genesis();
