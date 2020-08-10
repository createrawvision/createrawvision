<?php

namespace crv\dashboard;

add_action( 'genesis_entry_content', __NAMESPACE__ . '\show_dashboard' );

add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_style( 'daily-dish-dashboard-style', CHILD_URL . '/style-dashboard.css', array(), CHILD_THEME_VERSION );
	}
);

/**
 * Show username in title.
 */
add_filter(
	'genesis_post_title_text',
	function( $title ) {
		if ( ! is_user_logged_in() ) {
			return $title;
		}
		$name = get_userdata( get_current_user_id() )->first_name;
		if ( ! $name ) {
			return $title;
		}
		return 'Hallo, ' . $name . '!';
	}
);

function show_dashboard() {
	echo wp_kses_post( rcp_restricted_message_pending_verification( '' ) );
	echo '<div class="dashboard-container full-width">';

	$sections = array(
		'overview',
		'recipes',
		'course',
		'tutorials',
		// 'community',
		'blog',
		'settings',
		'support',
		'further',
	);

	foreach ( $sections as $section ) {
		echo '<div class="section-wrapper"><section class="' . esc_attr( $section ) . '">';
		call_user_func( __NAMESPACE__ . '\\show_' . $section );
		echo '</section></div>';
	}

	echo '</div>';
}

function show_overview() {
	echo '<h2>Starte Hier</h2>';
	echo '<ul>';
	echo '<li><a href="' . esc_url( get_permalink( get_page_by_path( 'einfuehrung' ) ) ) . '"><button>Zur Einführung</button></a></li>';
	echo '<li><a href="' . esc_url( get_category_link( 4269 ) ) . '"><button>Zur Übersicht</button></a></li>';
	echo '</ul>';
}

function show_recipes() {
	echo '<h2>Rohkost Rezepte</h2>';
	echo '<div class="recipes__container">';
	// show_recent_recipes();
	?>
	<ul class="recipes__list">
		<li><a href="<?php echo esc_url( get_term_link( 'rezepte', 'category' ) ); ?>"><button>Rezepte nach Kategorien</button></a></li>
		<li><a href="<?php the_permalink( get_page_by_path( 'neue-rezepte' ) ); ?>"><button>Neue Rezepte</button></a></li>
		<li><a href="<?php the_permalink( get_page_by_path( 'lesezeichen' ) ); ?>"><button>Deine Lieblingsrezepte</button></a></li>
		<li><a href="<?php the_permalink( get_page_by_path( 'beliebte-rezepte' ) ); ?>"><button>Beliebte Rezepte</button></a></li>
		<li><a href="<?php the_permalink( get_page_by_path( 'suche' ) ); ?>"><button>Rezepte suchen</button></a></li>
	</ul>
	<?php
	echo '</div>';
}

function show_recent_recipes() {
	$recipe_cat_id  = 5869;
	$recent_recipes = get_posts(
		array(
			'cat'          => $recipe_cat_id,
			'numberposts'  => 4,
			'meta_key'     => '_thumbnail_id',
			'meta_compare' => 'EXISTS',
		)
	);
	echo '<div class="recipes__recent"><a href="' . esc_url( get_permalink( get_page_by_path( 'neue-rezepte' ) ) ) . '"><span>Neue Rezepte</span></a>';
	foreach ( $recent_recipes as $post ) {
		echo '<div class="recipes__overlay">' . get_the_post_thumbnail( $post, 'thumbnail-portrait', array( 'class' => 'recipes__image' ) ) . '</div>';
	}
	echo '</div>';
}

function show_community() {
	/** @todo */
	echo '<h2 style="color: grey;">Community (demnächst verfügbar)</h2>';
}

function show_tutorials() {
	$tutorials_category_id = get_category_by_slug( 'tipps-tutorials' )->term_id ?? 5287;
	$courses               = get_categories( array( 'parent' => $tutorials_category_id ) );
	?>
	<h2>Rohkost Tipps & Tutorials</h2>
	<ul>
	<?php
	foreach ( $courses as $course ) {
		echo '<li><a href="' . esc_url( get_category_link( $course ) ) . '"><button>' . esc_html( $course->name ) . '</button></a></li>';
	}
	?>
	</ul>
	<?php
}

function show_course() {
	echo '<h2>Dein Weg Zur Rohkost Leicht Gemacht</h2>';
	echo '<ul><li><a href="' . esc_url( get_category_link( 5792 ) ) . '"><button>Zum Kurs</button></a></li></ul>';
}

function show_blog() {
	$blog_category_id = get_category_by_slug( 'blog' )->term_id;
	$categories       = get_categories( array( 'parent' => $blog_category_id ) );
	?>
	<h2>Blog</h2>
	<ul>
	<?php
	foreach ( $categories as $category ) {
		echo '<li><a href="' . esc_url( get_category_link( $category ) ) . '"><button>' . esc_html( $category->name ) . '</button></a></li>';
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
		<!-- <li><button>Q&As</button></li> -->
		<!-- @todo -->
		<!-- <li><button>Kommende Veranstaltungen</button></li> -->
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'unsere-vision' ) ) ); ?>"><button>Unsere Vision</button></a></li>
	</ul>
	<?php
}

function show_settings() {
	global $rcp_options;
	?>
	<h2>Einstellungen</h2>
	<ul>
		<li><a href="<?php the_permalink( $rcp_options['edit_profile'] ); ?>"><button>Profil bearbeiten</button></a></li>
		<li><a href="<?php the_permalink( $rcp_options['account_page'] ); ?>"><button>Mitgliedschaft/Zahlungen verwalten</button></a></li>
	</ul>
	<?php
}

function show_support() {
	?>
	<h2>Support</h2>
	<ul>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'faqs' ) ) ); ?>"><button>Häufige Fragen</button></a></li>
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'kontaktformular' ) ) ); ?>"><button>Kontakt</button></a></li>
		<li><a href="#header-search-wrap" class="toggle-header-search"><button>Website durchsuchen</button></a></li>
	</ul>
	<?php
}

genesis();
