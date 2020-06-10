<?php

/**
 * Return the form for filtering recipe posts
 */
function crv_recipe_filter_form()
{
  ob_start();
?><form action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST" id="recipe-filter">
    <input type="text" name="search" id="search" placeholder="Suchbegriff eingeben...">
    <?php
    crv_show_taxonomy_dropdown('wprm_ingredient', 'Zutat auswählen...');
    crv_show_taxonomy_dropdown('wprm_difficulty', 'Schwierigkeitsgrad auswählen...');
    crv_show_taxonomy_dropdown('wprm_course', 'Gang/Typ auswählen...');
    crv_show_taxonomy_dropdown('wprm_cuisine', 'Küche auswählen...');
    crv_show_taxonomy_dropdown('wprm_keyword', 'Schlagwort auswählen...');
    crv_show_taxonomy_dropdown('wprm_equipment', 'Ausstattung auswählen...');
    ?>

    <fieldset>
      <legend>Nach Datum sortieren</legend>
      <label><input type="radio" name="date" value="DESC" checked /> Neueste zuerst</label>
      <label><input type="radio" name="date" value="ASC" /> Älteste zuerst</label>
    </fieldset>

    <label><input type="checkbox" name="free" /> Nur kostenfreie Rezepte anezeigen</label>

    <button>Rezepte filtern</button>
    <input type="hidden" name="action" value="crv_post_filter">
  </form>
  <div id="filter_results"></div>
  <?php
  return ob_get_clean();
}

/**
 * Show select element for taxonomy  
 * name: `taxonomyfilter_{$taxonomy_name}`
 */
function crv_show_taxonomy_dropdown($taxonomy_name, $message)
{
  wp_dropdown_categories([
    'hierarchical' => TRUE,
    'orderby' => 'name',
    'taxonomy' => $taxonomy_name,
    'name' => 'taxonomyfilter_' . $taxonomy_name,
    'show_option_all' => $message
  ]);
}

add_action('wp_ajax_crv_post_filter', 'crv_filter_function');
add_action('wp_ajax_nopriv_crv_post_filter', 'crv_filter_function');

/**
 * Filters recipes by posted values
 * - date: ASC or DESC
 * - search: search term
 * - taxnonmyfilter_{taxomomy}
 * - free: when true, only show free content
 */
function crv_filter_function()
{
  $args = [
    'post_type' => 'wprm_recipe',
    'orderby' => 'date',
    'order' => $_POST['date'],
    's' => $_POST['search'],
    'fields' => 'ids',
    'nopaging' => TRUE
  ];

  $taxonomyfilter_keys = array_filter(array_keys($_POST), function ($key) {
    return $_POST[$key] && FALSE !== strpos($key, 'taxonomyfilter_');
  });
  $args['tax_query'] = array_map(function ($key) {
    $taxonomy_name = str_replace('taxonomyfilter_', '', $key);
    return [
      'taxonomy' => $taxonomy_name,
      'field' => 'id',
      'terms' => $_POST[$key]
    ];
  }, $taxonomyfilter_keys);
  $args['tax_query']['relation'] = 'AND';

  $recipe_ids = get_posts($args);

  $post_ids = array_map(function ($recipe_id) {
    return WPRM_Recipe_Manager::get_recipe($recipe_id)->parent_post_id();
  }, $recipe_ids);

  if (isset($_POST['free']) && $_POST['free']) {
    $post_ids = get_posts([
      'nopaging' => TRUE,
      'include' => $post_ids,
      'tax_query' => [
        'relation' => 'OR',
        [
          'taxonomy' => 'category',
          'field' => 'slug',
          'terms' => 'member',
          'operator' => 'NOT IN'
        ],
        [
          'taxonomy' => 'category',
          'field' => 'slug',
          'terms' => 'vegane-rezepte'
        ]
      ]
    ]);
  }

  array_map(function ($post_id) {
    $link = get_permalink($post_id);
    $title = get_the_title($post_id);
  ?>
    <article class="entry">
      <header class="entry-header">
        <h2 class="entry-title">
          <a class="entry-title-link" href="<?php echo $link; ?>">
            <?php echo $title; ?>
          </a>
        </h2>
      </header>
      <?php if (has_post_thumbnail($post_id)) : ?>
        <div class="entry-content">
          <a class="entry-image-link" href="<?php echo $link; ?>">
            <?php echo get_the_post_thumbnail($post_id); ?>
          </a>
        </div>
      <?php endif; ?>
    </article>
<?php
  }, $post_ids);

  die();
}
