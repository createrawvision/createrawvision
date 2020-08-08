<?php

namespace crv\dashboard;

add_action( 'genesis_entry_content', __NAMESPACE__ . '\show_dashboard' );

function show_dashboard() {
	echo rcp_restricted_message_pending_verification( '' );
	echo '<div class="dashboard-container">';

	$sections = array(
		'show_overview',
		'show_recipes',
		'show_tutorials',
		'show_course',
		'show_community',
		'show_blog',
		'show_further',
		'show_settings',
		'show_support',
	);

	foreach ( $sections as $section ) {
		echo '<section>';
		call_user_func( __NAMESPACE__ . '\\' . $section );
		echo '</section>';
	}

	echo '</div>';
}

function show_overview() {
	echo '<h2><a href="' . esc_url( get_category_link( 4269 ) ) . '">Mitgliederbereich-Übersicht</a></h2>';
}

function show_recipes() {
	?>
	<h2>Rohkost Rezepte</h2>
	<ul>
		<li><a href="<?php echo esc_url( get_term_link( 'rezepte', 'category' ) ); ?>">Rezepte nach Kategorien</a></li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'suche' ) ) ); ?>">Rezepte suchen</a></li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'neue-rezepte' ) ) ); ?>">Neue Rezepte</a></li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'lesezeichen' ) ) ); ?>">Deine Lieblingsrezepte</a></li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'beliebte-rezepte' ) ) ); ?>">Beliebte Rezepte</a></li>
	</ul>
	<?php
}

function show_community() {
	/** @todo */
	echo '<h2 style="color: grey;">Community (demnächst verfügbar)</h2>';
}

function show_tutorials() {
	$tutorials_category_id = get_category_by_slug( 'tipps-tutorials' )->term_id ?? 5287;
	$courses               = get_categories( array( 'parent' => $tutorials_category_id ) );
	?>
	<h2><a href="<?php echo esc_url( get_category_link( $tutorials_category_id ) ); ?>">Rohkost Tipps & Tutorials</a></h2>
	<ul>
	<?php
	foreach ( $courses as $course ) {
		echo '<li><a href="' . esc_url( get_category_link( $course ) ) . '">' . esc_html( $course->name ) . '</a></li>';
	}
	?>
	</ul>
	<?php
}

function show_course() {
	echo '<section><h2><a href="' . esc_url( get_category_link( 5792 ) ) . '">Dein Weg Zur Rohkost Leicht Gemacht - Kurs</a></h2></section>';
}

function show_blog() {
	$blog_category_id = get_category_by_slug( 'blog' )->term_id;
	$categories       = get_categories( array( 'parent' => $blog_category_id ) );
	?>
	<h2><a href="<?php echo esc_url( get_category_link( $blog_category_id ) ); ?>">Blog</a></h2>
	<ul>
	<?php
	foreach ( $categories as $category ) {
		echo '<li><a href="' . esc_url( get_category_link( $category ) ) . '">' . esc_html( $category->name ) . '</a></li>';
	}
	?>
	</ul>
	<?php
}

function show_further() {
	?>
	<h2>Weiteres</h2>
	<ul>
		<!-- @todo -->
		<li>Q&As</li>
		<!-- @todo -->
		<li>Kommende Veranstaltungen</li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'unsere-vision' ) ) ); ?>">Unsere Vision</a></li>
	</ul>
	<?php
}

function show_settings() {
	global $rcp_options;
	?>
	<h2>Einstellungen</h2>
	<ul>
		<li><a href="<?php the_permalink( $rcp_options['edit_profile'] ); ?>">Profil bearbeiten</a></li>
		<li><a href="<?php the_permalink( $rcp_options['account_page'] ); ?>">Mitgliedschaft verwalten</a></li>
	</ul>
	<?php
}

function show_support() {
	?>
	<h2>Support</h2>
	<ul>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'faqs' ) ) ); ?>">Häufige Fragen</a></li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'kontaktformular' ) ) ); ?>">Kontakt</a></li>
		<li><a href="#header-search-wrap" class="toggle-header-search">Website durchsuchen</a></li>
	</ul>
	<?php
}

genesis();
