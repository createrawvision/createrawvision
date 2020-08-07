<?php
/**
 * Adds a help button to every page, which opens a popup with more information.
 */

namespace Crv\HelpPopup;

add_action( 'wp_footer', __NAMESPACE__ . '\\show_markup' );

/**
 * Shows the markup for the
 */
function show_markup() {
	?> 
	<section class="crv-help-popup">
		<button class="crv-help-popup__button"></button>
		<div class="crv-help-popup__modal">
			<h3 class="crv-help-popup__title">Brauchst du Hilfe?</h3>
			<p class="crv-help-popup__text">
				Schau dir bitte zuerst unsere <a href="<?php the_permalink( get_page_by_path( 'faqs' ) ); ?>">häufig gestellten Fragen</a> an.<br>
				<!-- @todo Link to introduction. -->
				Oder versuche deine Antwort in <a href="<?php the_permalink( get_page_by_path( '' ) ); ?>">unserer Einführung</a> zu finden.<br>
				Das geht meistens schneller, als auf eine Antwort zu warten.
			</p>
			<p class="crv-help-popup__text">
				Falls du immer noch keine Antwort hast, kannst du uns gerne 
				<a href="<?php the_permalink( get_page_by_path( 'kontaktformular' ) ); ?>">hier persönlich fragen</a>.
			</p>
		</div>
	</section>
	<?php
}