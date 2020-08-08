<?php

/**
 * Replaces the default loop for faqs page
 */
function crv_faqs_loop() {
	jw_enqueue_faq_scripts_styles();
	jw_display_faq_search();
	jw_display_faqs();
	jw_display_contact_form_link();
}

/**
 * Registers styles and scripts for FAQs
 */
function jw_enqueue_faq_scripts_styles() {
	wp_enqueue_style( 'faq', get_stylesheet_directory_uri() . '/lib/faqs/style-faq.css', array(), null );
	wp_enqueue_script( 'faq', get_stylesheet_directory_uri() . '/lib/faqs/script-faq.js', array(), null );
}

/**
 * Display a search input to search FAQs
 */
function jw_display_faq_search() {
	?>
  <h2>Wie lautet deine Frage?</h2>
  <form class="search-form" method="get" action="<?php the_permalink(); ?>" role="search">
	<label class="search-form-label screen-reader-text" for="faq-searchform"><?php esc_html_e( __( 'Häufig gestellte Fragen durchsuchen' ) ); ?></label>
	<input class="search-form-input" type="search" name="faq_search" id="faq-searchform" placeholder="<?php esc_attr_e( __( 'Häufig gestellte Fragen durchsuchen' ) ); ?>">
  </form>
	<?php
	if ( ! empty( get_query_var( 'faq_search' ) ) ) {
		?>
   <a href="<?php the_permalink(); ?>">Suche zurücksetzen</a>
	<?php } ?>
  <hr>
	<?php
}

/**
 * Display FAQs grouped by category
 */
function jw_display_faqs() {
	// Disable relevanssi search.
	remove_filter( 'posts_request', 'relevanssi_prevent_default_request' );
	remove_filter( 'the_posts', 'relevanssi_query', 99 );

	// Count Total FAQs to be able to display a message for empty search.
	$faq_count = 0;

	$terms = get_terms(
		array(
			'taxonomy' => 'faq_category',
			'orderby'  => 'ID',
			'order'    => 'ASC',
		)
	);

	// Loop all categories.
	foreach ( $terms as $term ) :

		$query_args = array(
			'post_type'      => 'faq',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => get_query_var( 'faq_search' ),
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'faq_category',
					'terms'    => $term->term_id,
				),
			),
		);

		$query = new WP_Query( $query_args );

		// The FAQ loop.
		if ( $query->have_posts() ) :
			?>
		<h2><?php echo $term->name; ?></h2>
		<ul class="faq">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$faq_count++;
				?>
			<li class="faq__item">
				<h3 class="faq__title"><?php the_title(); ?></h3>
				<div class="faq__body"><?php the_content(); ?></div>
			</li>
				<?php
			endwhile;
			?>
		</ul>
			<?php
			wp_reset_postdata();
		else :
			// Hide category, when there are no FAQs matching.
		endif;
	endforeach;

	// Message, when no FAQs displayed.
	if ( 0 === $faq_count ) {
		echo '<p>' . esc_html__( 'Keine Antworten gefunden.' ) . '</p>';
	}
}

/**
 * Display contact form
 */
function jw_display_contact_form_link() {
	?>
  <hr>
  <h2>Keine Antworten gefunden?</h2>
  <p>Stell uns deine Frage einfach durch unser <a href="<?php the_permalink( get_page_by_path( 'kontaktformular' ) ); ?>">Kontaktformular</a>.</p>
	<?php
}
