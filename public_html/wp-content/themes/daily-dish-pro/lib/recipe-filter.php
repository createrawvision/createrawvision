<?php
/**
 * Functions for the advanced recipe filter (for displaying and request handling).
 */

/**
 * Return the form for filtering recipe posts
 */
function crv_recipe_filter_form() {
	ob_start(); ?>
	<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="POST" id="recipe-filter">
		<label for="search">Suchbegriff eingeben...
			<input type="text" name="search" id="search" placeholder="z. B. Zutat, Ausstattung oder Zubereitungsschritt">
		</label>
		<?php
		crv_show_taxonomy_dropdown( 'wprm_difficulty', 'Schwierigkeitsgrad auswählen...', 'Alle Schwierigkeitsgrade' );
		crv_show_taxonomy_dropdown( 'wprm_course', 'Gang/Typ auswählen...', 'Alle Gänge/Typen' );
		crv_show_taxonomy_dropdown( 'wprm_cuisine', 'Küche auswählen...', 'Alle Küchen' );
		?>

		<fieldset>
			<legend>Nach Datum sortieren</legend>
			<label><input type="radio" name="date" value="DESC" checked /> Neueste zuerst</label>
			<label><input type="radio" name="date" value="ASC" /> Älteste zuerst</label>
		</fieldset>

		<label><input type="checkbox" name="free"> Nur kostenfreie Rezepte anzeigen</label>

		<button type="submit">Rezepte filtern</button>
		<input type="hidden" name="action" value="crv_post_filter">
	</form>
	<div id="filter_results" class="crv-grid" style="margin-top: 3rem;"></div>
	<p class="recipe-filter__loading-hint">Weitere Ergebnisse werden geladen...</p>
	<?php
	return ob_get_clean();
}

/**
 * Show select element for taxonomy
 * name: `taxonomyfilter_{$taxonomy_name}`
 */
function crv_show_taxonomy_dropdown( $taxonomy_name, $message_label, $message_option ) {
	$name = 'taxonomyfilter_' . $taxonomy_name;
	echo '<label for="' . esc_attr( $name ) . '">' . esc_html( $message_label );

	wp_dropdown_categories(
		array(
			'hierarchical'    => true,
			'orderby'         => 'name',
			'taxonomy'        => $taxonomy_name,
			'name'            => $name,
			'show_option_all' => $message_option,
		)
	);

	echo '</label>';
}

add_action( 'wp_ajax_crv_post_filter', 'crv_filter_recipes' );
add_action( 'wp_ajax_nopriv_crv_post_filter', 'crv_filter_recipes' );

/**
 * Filters recipes by posted values
 * - date: ASC or DESC
 * - search: search term
 * - taxnonmyfilter_{taxomomy}
 * - free: when true, only show free content
 *
 * @todo maybe cache results (since the same thing may get computed on multiple requests)
 */
function crv_filter_recipes() {
	// WP_Query args for getting recipes.
	$args = array(
		'post_type' => 'wprm_recipe',
		'orderby'   => 'date',
		'order'     => isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : 'DESC',
		's'         => isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '',
		'fields'    => 'ids',
		'nopaging'  => true,
	);

	// Build tax query for all non-empty keys 'taxonomyfilter_{taxonomy}' in $_POST.
	$taxonomyfilter_keys           = array_filter(
		array_keys( $_POST ),
		function ( $key ) {
			return $_POST[ $key ] && false !== strpos( $key, 'taxonomyfilter_' );
		}
	);
	$args['tax_query']             = array_map(
		function ( $key ) {
			$taxonomy_name = str_replace( 'taxonomyfilter_', '', $key );
			return array(
				'taxonomy' => $taxonomy_name,
				'field'    => 'id',
				'terms'    => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : '',
			);
		},
		$taxonomyfilter_keys
	);
	$args['tax_query']['relation'] = 'AND';

	$recipe_ids = get_posts( $args );

	// Get containing posts.
	$post_ids = array_map(
		function ( $recipe_id ) {
			return WPRM_Recipe_Manager::get_recipe( $recipe_id )->parent_post_id();
		},
		$recipe_ids
	);

	// Remove all restricted posts from list.
	if ( isset( $_POST['free'] ) && $_POST['free'] ) {
		$post_ids = crv_strip_restricted_posts( $post_ids );
	}

	// Paginate response.
	$posts_count        = count( $post_ids );
	$page               = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 0;
	$posts_per_page     = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 12;
	$max_num_pages      = intdiv( $posts_count - 1, $posts_per_page ) + 1;
	$post_ids_to_return = array_slice( $post_ids, $page * $posts_per_page, $posts_per_page );

	// Get formatted results.
	ob_start();
	foreach ( $post_ids_to_return as $post_id ) {
		$link     = get_permalink( $post_id );
		$title    = get_the_title( $post_id );
		$image_id = get_post_thumbnail_id( $post_id );

		require __DIR__ . '/../templates/grid.php';
	}
	$html_content = ob_get_clean();

	// Respond.
	header( 'Content-type: application/json' );
	echo wp_json_encode(
		array(
			'html'  => $html_content,
			'page'  => $page,
			'pages' => $max_num_pages,
			'count' => $posts_count,
		)
	);

	die();
}
