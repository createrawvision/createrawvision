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
		wp_enqueue_style( 'crv-dashboard-style', CHILD_URL . '/style-dashboard.css', array(), CHILD_THEME_VERSION );
		wp_enqueue_script( 'crv-reciperequest', CHILD_URL . '/js/reciperequest.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
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
 * Show entry-header subtitle.
 */
add_action(
	'genesis_entry_header',
	function() {
		echo '<p class="entry-subtitle">Willkommen an deinem Ort für Rohkost</p>';
	}
);

/**
 * Echo the whole dashboard content.
 */
function show_dashboard() {
	echo wp_kses_post( rcp_restricted_message_pending_verification( '' ) );
	echo '<div class="dashboard-container full-width">';

	$sections = array(
		'overview',
		'recipes',
		'reciperequest',
		'further',
		'support',
		'settings',
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
		?> 
		<li class="overview__item">
			<a href="<?php echo esc_url( get_category_link( $category['id'] ) ); ?>">
				<?php echo wp_get_attachment_image( $category['image_id'], 'thumbnail-portrait', false, array( 'class' => 'overview__image' ) ); ?>
				<p class="overview__title"><?php echo esc_html( $category['title'] ); ?></p>
			</a>
		</li>
		<?php
	}
	echo '</ul>';
}

/**
 * Show everything related to recipes.
 */
function show_recipes() {
	?>
	<ul class="dashboard-cards">
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Deine Lieblingsrezepte</h3>
			<p class="dashboard-cards__text">Um dir das Finden der Rezepte, die du am liebsten zubereitest zu erleichtern, kannst du hier deine Lieblingsrezepte abspeichern und einsehen.</p>
			<a href="<?php the_permalink( get_page_by_path( 'lesezeichen' ) ); ?>" class="dashboard-cards__link">Zu deinen Lieblingsrezepten</a>
		</li>
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Rezeptsuche</h3>
			<p class="dashboard-cards__text">Du bist auf der Suche nach einem bestimmten Rezept? Dann benutz unsere besondere Suchfunktion. Du kannst nach Kategorie, Schwierigkeitsgrad und vielem mehr filtern.</p>
			<a href="<?php the_permalink( get_page_by_path( 'suche' ) ); ?>" class="dashboard-cards__link">Zur Rezeptsuche</a>
		</li>
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Neue Rezepte</h3>
			<p class="dashboard-cards__text">Bei uns bekommst du regelmäßig neue Rezepte. Hier findest du unsere neuesten Rezepte.</p>
			<a href="<?php the_permalink( get_page_by_path( 'neue-rezepte' ) ); ?>" class="dashboard-cards__link">Zu den neuen Rezepten</a>
		</li>
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Die beliebtesten Rezepte</h3>
			<p class="dashboard-cards__text">Welche Rezepte sind momentan die beliebtesten? Das kannst du hier herausfinden.</p>
			<a href="<?php the_permalink( get_page_by_path( 'beliebte-rezepte' ) ); ?>" class="dashboard-cards__link">Zu den beliebten Rezepten</a>
		</li>
	</ul>
	<?php
}

/**
 * Shows the recipe wish box.
 */
function show_reciperequest() {
	?>
	<h2 class="reciperequest__heading">Rezept&shy;wunschbox</h2>
	<p>
		Vermisst du eines deiner Lieblingsrezepte in Rohkost-Variante in unserem Mitgliederbereich? 
		Dann kannst du hier deinen Wunsch abschicken. 
		Wir bemühen uns, dein Rezept in eine roh-vegane Variante umzuwandeln.
	</p>
	<form class="reciperequest__box" method="POST" action="<?php echo esc_url( rest_url( 'supportcandy/v1/tickets/addRegisteredUserTicket' ) ); ?>">
		<input name="ticket_subject" class="reciperequest__name" type="text" placeholder="Rezepttitel" aria-label="Der Titel des gewünschten Rezepts">
		<textarea name="ticket_description" class="reciperequest__textarea"  cols="30" rows="5" placeholder="Kommentar" aria-label="Ein Kommentar zu deinem Rezeptwunschs"></textarea>
		<button class="reciperequest_button" type="submit">Wunsch abschicken</button>
		<input type="hidden" name="ticket_category" value="6192">
		<?php wp_nonce_field( 'wp_rest' ); ?>
		<div class="reciperequest__waiting reciperequest__modal">
			Dein Wunsch wird versendet...
		</div>
		<div class="reciperequest__failed reciperequest__modal">
			<p class="reciperequest__modal__heading">Etwas ging schief...</p>
			<p>Stell sicher, dass du angemeldet bist.</p>
			<span class="reciperequest__back">Zurück</span>
		</div>
		<div class="reciperequest__success reciperequest__modal">
			<p class="reciperequest__modal__heading">Danke!</p>
			<p>
				<?php
				if ( is_user_logged_in() ) {
					echo 'Lieber ' . esc_html( get_userdata( get_current_user_id() )->first_name ) . ',<br>';
				}
				?>
				Wir haben deinen Wunsch erhalten und werden dir so schnell wie möglich Bescheid geben, 
				ob es für uns möglich ist, dein Wunschrezept in Rohkostqualität nachzumachen.
				Je nach Wunschaufkommen, kann das aber einige Zeit dauern.
			</p>
			<span class="reciperequest__back">Zurück</span>
		</div>
	</form>
					<?php
}

				/**
				 * Show things, that don't fit somewhere else.
				 *
				 * @todo Q&As and events
				 */
function show_further() {
	?>
	<ul class="dashboard-cards">
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Einführung</h3>
			<p class="dashboard-cards__text">Hier zeigen wir dir wie du dich am besten in unserem Mitgliederbereich zurechtfindest. Dadurch kannst du alle Vorteile deiner Mitgliedschaft ausschöpfen.</p>
			<a href="<?php the_permalink( get_page_by_path( 'einfuehrung' ) ); ?>" class="dashboard-cards__link">Zur Einführung</a>
		</li>
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Unsere Vision</h3>
			<p class="dashboard-cards__text">Warum machen wir das alles? Finde heraus, was uns bewegt und warum wir den Mitgliederbereich ins Leben gerufen haben.</p>
			<a href="<?php the_permalink( get_page_by_path( 'unsere-vision' ) ); ?>" class="dashboard-cards__link">Zu unserer Vision</a>
		</li>
	</ul>
					<?php
}

				/**
				 * Show support related items.
				 */
function show_support() {
	?>
	<h2 class="support__heading">Hilfe & Support</h2>
	<ul class="support__list">
		<li class="support__item"><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'faqs' ) ) ); ?>">Zu den häufigen Fragen</a></li>
		<li class="support__item"><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'kontaktformular' ) ) ); ?>">Uns jetzt kontaktieren</a></li>
	</ul>
					<?php
}

				/**
				 * Show all settings for the user.
				 */
function show_settings() {
	global $rcp_options;
	?>
	<h2 class="settings__heading">Einstellungen</h2>
	<ul class="dashboard-cards">
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Profil bearbeiten</h3>
			<p class="dashboard-cards__text">In diesem Bereich kannst du deine Profildaten wie Benutzername, Passwort und E-Mail-Adresse ändern.</p>
			<a href="<?php the_permalink( $rcp_options['edit_profile'] ); ?>" class="dashboard-cards__link">Profil bearbeiten</a>
		</li>
		<li class="dashboard-cards__item">
			<h3 class="dashboard-cards__title">Mitgliedschaft / Zahlungen verwalten</h3>
			<p class="dashboard-cards__text">Hier kannst du den Status deiner Mitgliedschaft und alle Zahlungen einsehen und deine Mitgliedschaft bearbeiten.</p>
			<a href="<?php the_permalink( $rcp_options['account_page'] ); ?>" class="dashboard-cards__link">Mitgliedschaft verwalten</a>
		</li>
	</ul>
					<?php
}

				// Start the engine.
				genesis();
