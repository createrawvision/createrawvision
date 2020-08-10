<?php

namespace crv\dashboard;

/**
 * Add content to entry_content.
 */
add_action( 'genesis_entry_content', __NAMESPACE__ . '\show_dashboard', 9 );

/**
 * Enqueue dashboard styles.
 */
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

/**
 * Make entry header full-width
 */
add_filter(
	'genesis_attr_entry-header',
	function( $attributes ) {
		if ( isset( $attributes['class'] ) ) {
			$attributes['class'] .= ' full-width';
		} else {
			$attributes['class'] = 'full-width';
		}
		return $attributes;
	}
);

/**
 * Show subtitle.
 */
add_action(
	'genesis_entry_header',
	function() {
		echo '<p class="entry-subtitle">Willkommen an deinem Ort für Rohkost</p>';
	}
);

function show_dashboard() {
	echo wp_kses_post( rcp_restricted_message_pending_verification( '' ) );
	echo '<div class="dashboard-container full-width">';

	$sections = array(
		'overview',
		'recipes',
		// 'community',
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

/**
 * Show a custom overview of the 'member' category
 */
function show_overview() {
	$categories = array(
		array(
			'id'       => 5792,
			'title'    => 'Zum Einsteigerkurs',
			'image_id' => 21027,
		),
		array(
			'id'       => 5869,
			'title'    => 'Zu allen Rezepten',
			'image_id' => 20833,
		),
		array(
			'id'       => 5287,
			'title'    => 'Zu den Tipps & Tutorials',
			'image_id' => 20243,
		),
	);

	echo '<ul class="overview__list">';
	foreach ( $categories as $category ) {
		echo '<li class="overview__item"><a href="' . get_category_link( $category['id'] ) . '">';
		echo wp_get_attachment_image( $category['image_id'], 'thumbnail-portrait', false, array( 'class' => 'overview__image' ) );
		echo '<p class="overview__title">' . $category['title'] . '</p>';
		echo '</a></li>';
	}
	echo '</ul>';
}

/**
 * Show everything related to recipes.
 */
function show_recipes() {
	?>
	<ul class="recipes__list">
		<li class="recipes__card">
			<h3 class="recipes__title">Deine Lieblingsrezepte</h3>
			<p class="recipes__text">Um dir das Finden der Rezepte, die du am liebsten zubereitest zu erleichtern, kannst du hier deine Lieblingsrezepte abspeichern und einsehen.</p>
			<a href="<?php the_permalink( get_page_by_path( 'neue-rezepte' ) ); ?>">
				<span class="recipes__link">Zu deinen Lieblingsrezepten</span>
		</a></li>
		<li class="recipes__card">
			<h3 class="recipes__title">Rezeptsuche</h3>
			<p class="recipes__text">Du bist auf der Suche nach einem bestimmten Rezept? Dann benutz unsere besondere Suchfunktion. Du kannst nach Kategorie, Schwierigkeitsgrad und vielem mehr filtern.</p>
			<a href="<?php the_permalink( get_page_by_path( 'lesezeichen' ) ); ?>">
				<span class="recipes__link">Zur Rezeptsuche</span>
		</a></li>
		<li class="recipes__card">
			<h3 class="recipes__title">Neue Rezepte</h3>
			<p class="recipes__text">Bei uns bekommst du regelmäßig neue Rezepte. Hier findest du unsere neuesten Rezepte.</p>
			<a href="<?php the_permalink( get_page_by_path( 'suche' ) ); ?>">
				<span class="recipes__link">Zu den neuen Rezepten</span>
		</a></li>
		<li class="recipes__card">
			<h3 class="recipes__title">Die beliebtesten Rezepte</h3>
			<p class="recipes__text">Welche Rezepte sind momentan die beliebtesten? Das kannst du hier herausfinden.</p>
			<a href="<?php the_permalink( get_page_by_path( 'beliebte-rezepte' ) ); ?>">
				<span class="recipes__link">Zu den beliebten Rezepten</span>
		</a></li>
	</ul>
	<?php
}

function show_community() {
	/** @todo */
	echo '<h2 style="color: grey;">Community (demnächst verfügbar)</h2>';
}

function show_further() {
	?>
	<h2>Weiteres</h2>
	<ul>
		<!-- @todo -->
		<!-- <li><button>Q&As</button></li> -->
		<!-- @todo -->
		<!-- <li><button>Kommende Veranstaltungen</button></li> -->
		<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'einfuehrung' ) ) ); ?>"><button>Zur Einführung</button></a></li>
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
