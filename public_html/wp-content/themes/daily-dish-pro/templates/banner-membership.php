<?php
/**
 * Shows a banner with countdown and CTA
 */

wp_enqueue_script( 'crv-countdown' );
?>

<div class="banner-membership">
	<div class="countdown full-width">
		<div class="countdown__timer">
			<div class="countdown__timer__item">
				<span class="countdown__timer__time-element countdown__timer__days"></span>
				<span class="countdown__timer__time-label">Tage</span>
			</div>
			<div class="countdown__timer__item">
				<span class="countdown__timer__time-element countdown__timer__hours"></span>
				<span class="countdown__timer__time-label">Stunden</span>
			</div>
			<div class="countdown__timer__item">
				<span class="countdown__timer__time-element countdown__timer__minutes"></span>
				<span class="countdown__timer__time-label">Minuten</span>
			</div>
			<div class="countdown__timer__item">
				<span class="countdown__timer__time-element countdown__timer__seconds"></span>
				<span class="countdown__timer__time-label">Sekunden</span>
			</div>
		</div>
		<ul class="countdown__description">
			<li>500+ einzigartige Rohkost Rezepte</li>
			<li>50+ Tipps & Tutorials</li>
			<li>Rohkost Kurs für Einsteiger</li>
		</ul>
		<a href="<?php echo esc_url( home_url() ); ?>">
			<button class="countdown__button cta-button">
				Jetzt mit Rohkost durchstarten!
			</button>
		</a>
	</div>
</div>
