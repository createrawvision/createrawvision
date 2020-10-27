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
		// Class is always set (at least context).
		$attributes['class'] .= ' full-width';
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
		'feedback',
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
	show_cards(
		array(
			array(
				'title'     => 'Deine Lieblingsrezepte',
				'text'      => 'Um dir das Finden der Rezepte, die du am liebsten zubereitest zu erleichtern, kannst du hier deine Lieblingsrezepte abspeichern und einsehen.',
				'url'       => get_permalink( get_page_by_path( 'lesezeichen' ) ),
				'link_text' => 'Zu deinen Lieblingsrezepten',
				'icon_name' => 'heart',
			),
			array(
				'title'     => 'Rezeptsuche',
				'text'      => 'Du bist auf der Suche nach einem bestimmten Rezept? Dann benutz unsere besondere Suchfunktion. Du kannst nach Kategorie, Schwierigkeitsgrad und vielem mehr filtern.',
				'url'       => get_permalink( get_page_by_path( 'suche' ) ),
				'link_text' => 'Zur Rezeptsuche',
				'icon_name' => 'search',
			),
			array(
				'title'     => 'Neue Rezepte',
				'text'      => 'Bei uns bekommst du regelmäßig neue Rezepte. Hier findest du unsere neuesten Rezepte.',
				'url'       => get_permalink( get_page_by_path( 'neue-rezepte' ) ),
				'link_text' => 'Zu den neuen Rezepten',
				'icon_name' => 'clock',
			),
			array(
				'title'     => 'Die beliebtesten Rezepte',
				'text'      => 'Welche Rezepte sind momentan die beliebtesten? Das kannst du hier herausfinden.',
				'url'       => get_permalink( get_page_by_path( 'beliebte-rezepte' ) ),
				'link_text' => 'Zu den beliebten Rezepten',
				'icon_name' => 'star',
			),
		)
	);
}

/**
 * Shows the recipe wish box.
 */
function show_reciperequest() {
	?>
	<h2 class="reciperequest__heading">Rezept&shy;wunschbox</h2>
	<p>
		Vermisst du eines deiner Lieblingsrezepte in Rohkost-Variante in unserem Mitgliederbereich?<br>
		Dann kannst du hier deinen Wunsch abschicken.
		Wir bemühen uns, dein Rezept in eine roh-vegane Variante umzuwandeln.
	</p>
	<form class="reciperequest__box" method="POST" action="<?php echo esc_url( rest_url( 'supportcandy/v1/tickets/addRegisteredUserTicket' ) ); ?>">
		<input name="ticket_subject" class="reciperequest__name" type="text" placeholder="Rezepttitel" aria-label="Der Titel des gewünschten Rezepts">
		<textarea name="ticket_description" class="reciperequest__textarea"  cols="30" rows="5" placeholder="Kommentar" aria-label="Ein Kommentar zu deinem Rezeptwunschs"></textarea>
		<button class="reciperequest_button" type="submit">Wunsch abschicken</button>
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
	show_cards(
		array(
			array(
				'title'     => 'Einführung',
				'text'      => 'Hier zeigen wir dir wie du dich am besten in unserem Mitgliederbereich zurechtfindest. Dadurch kannst du alle Vorteile deiner Mitgliedschaft ausschöpfen.',
				'url'       => get_permalink( get_page_by_path( 'einfuehrung' ) ),
				'link_text' => 'Zur Einführung',
				'icon_name' => 'card-text',
			),
			array(
				'title'     => 'Unsere Vision',
				'text'      => 'Warum machen wir das alles? Finde heraus, was uns bewegt und warum wir den Mitgliederbereich ins Leben gerufen haben.',
				'url'       => get_permalink( get_page_by_path( 'unsere-vision' ) ),
				'link_text' => 'Zu unserer Vision',
				'icon_name' => 'signpost',
			),
		)
	);
}

/**
 * Show support related items.
 */
function show_support() {
	echo '<h2 class="support__heading">Hilfe & Support</h2>';
	show_button_list(
		array(
			array(
				'text'      => 'Zu den häufigen Fragen',
				'url'       => get_permalink( get_page_by_path( 'faqs' ) ),
				'icon_name' => 'question',
			),
			array(
				'text'      => 'Uns jetzt kontaktieren',
				'url'       => get_permalink( get_page_by_path( 'kontaktformular' ) ),
				'icon_name' => 'envelope',
			),
		)
	);
}

/**
 * Show all settings for the user.
 */
function show_settings() {
	global $rcp_options;
	echo '<h2 class="settings__heading">Einstellungen</h2>';
	show_cards(
		array(
			array(
				'title'     => 'Profil bearbeiten',
				'text'      => 'In diesem Bereich kannst du deine Profildaten wie Benutzername, Passwort und E-Mail-Adresse ändern.',
				'url'       => get_permalink( $rcp_options['edit_profile'] ),
				'link_text' => 'Profil bearbeiten',
				'icon_name' => 'pencil-square',
			),
			array(
				'title'     => 'Mitgliedschaft / Zahlungen verwalten',
				'text'      => 'Hier kannst du den Status deiner Mitgliedschaft und alle Zahlungen einsehen und deine Mitgliedschaft bearbeiten.',
				'url'       => get_permalink( $rcp_options['account_page'] ),
				'link_text' => 'Mitgliedschaft verwalten',
				'icon_name' => 'credit-card',
			),
		)
	);
}

/**
 * Section with a feedback button linking to contact form.
 */
function show_feedback() {
	?>
	<h2 class="feedback__heading">Feedback / Rückmeldung</h2>
	<p class="feedback__text">
		Wir möchten dir deine Mitgliedschaft so hilfreich wie nur möglich machen.<br>
		Hilf uns dabei, das zu erreichen.
	</p>
	<?php
	show_button_list(
		array(
			array(
				'text'      => 'Sag uns hier, was wir verbessern können!',
				'url'       => add_query_arg( 'feedback', '', get_permalink( get_page_by_path( 'kontaktformular' ) ) ),
				'icon_name' => 'chat',
			),
		)
	);
}

/**
 * @param array $cards {
 *     @see show_card()
 * }
 */
function show_cards( $cards ) {
	echo '<ul class="dashboard-cards">';
	foreach ( $cards as $card ) {
		show_card( $card );
	}
	echo '</ul>';
}

/**
 * @param array $card {
 *     @type string $title
 *     @type string $text
 *     @type string $url
 *     @type string $link_text
 *     @type string $icon_name @see get_icon_url
 * }
 */
function show_card( $card ) {
	?>
	<li class="dashboard-cards__item">
		<div class="dashboard-cards__header">
			<?php maybe_show_icon( $card['icon_name'] ?? null ); ?>
			<h3 class="dashboard-cards__title">
				<?php echo esc_html( $card['title'] ); ?>
			</h3>
		</div>
		<p class="dashboard-cards__text">
			<?php echo esc_html( $card['text'] ); ?>
		</p>
		<a href="<?php echo esc_url( $card['url'] ); ?>" class="dashboard-cards__link">
			<?php echo esc_html( $card['link_text'] ); ?>
		</a>
	</li>
	<?php
}

/**
 * @param array $buttons {
 *    @see show_button()
 * }
 */
function show_button_list( $buttons ) {
	echo '<ul class="button-list">';
	foreach ( $buttons as $button ) {
		show_button( $button );
	}
	echo '</ul>';
}

/**
 * @param array $button {
 *    @type string $text
 *    @type string $url
 *    @type string $icon_name @see get_icon_url
 * }
 */
function show_button( $button ) {
	?>
	<li class="button-list__button">
		<a class="button-list__link" href="<?php echo esc_url( $button['url'] ); ?>">
			<?php maybe_show_icon( $button['icon_name'] ?? null ); ?>
			<?php echo esc_html( $button['text'] ); ?>
		</a>
	</li>
	<?php
}

/**
 * @param string $icon_name @see get_icon_url
 */
function maybe_show_icon( $icon_name ) {
	if ( ! $icon_name ) {
		return;
	}
	?>
	<svg class="dashboard__icon">
		<use href="<?php echo esc_url( get_icon_url( $icon_name ) ); ?>" />
	</svg>
	<?php
}

/**
 * @param string $icon_name The icons identifier in the spritesheet (without `#`).
 */
function get_icon_url( $icon_name ) {
	return CHILD_URL . '/images/dashboard-icons.svg#' . $icon_name;
}

// Start the engine.
genesis();
