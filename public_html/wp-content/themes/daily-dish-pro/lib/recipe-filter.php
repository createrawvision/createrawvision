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

		<button>Rezepte filtern</button>
		<input type="hidden" name="action" value="crv_post_filter">
	</form>
	<div id="filter_results" class="crv-grid" style="margin-top: 3rem;"></div>
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

	// Get containing posts
	$post_ids = array_map(
		function ( $recipe_id ) {
			return WPRM_Recipe_Manager::get_recipe( $recipe_id )->parent_post_id();
		},
		$recipe_ids
	);

	// Remove all restricted posts from list
	if ( isset( $_POST['free'] ) && $_POST['free'] ) {
		$post_ids = crv_strip_restricted_posts( $post_ids );
	}

	// Return formatted results
	array_map(
		function ( $post_id ) {
			$link  = get_permalink( $post_id );
			$title = get_the_title( $post_id );
			?>
		<article class="entry">
			<header class="entry-header">
				<h2 class="entry-title">
					<a class="entry-title-link" href="<?php echo esc_url( $link ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				</h2>
			</header>
			<div class="entry-content">
				<?php if ( has_post_thumbnail( $post_id ) ) : ?>
					<a class="entry-image-link" href="<?php echo esc_url( $link ); ?>">
						<?php echo get_the_post_thumbnail( $post_id ); ?>
					</a>
				<?php else : ?>
					<p>Kein Vorschaubild vorhanden...</p>
				<?php endif; ?>
			</div>
		</article>
			<?php
		},
		$post_ids
	);

	die();
}
