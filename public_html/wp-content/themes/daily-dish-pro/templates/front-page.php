<?php
global $rcp_options;
$registration_page_id = $rcp_options['registration_page'];
?>

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
	<p class="countdown__description">
		bis zur Veröffentlichung des Mitgliederbereichs
	</p>
	<a href="<?php the_permalink( $registration_page_id ); ?>">
		<button class="countdown__button cta-button">
			Jetzt anmelden
		</button>
	</a>
</div>

<div class="sticky-wrapper">
	<div class="countdown countdown--inline full-width">
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
		<a href="<?php the_permalink( $registration_page_id ); ?>">
			<button class="countdown__button cta-button">
				Jetzt anmelden
			</button>
		</a>
	</div>
</div>

<header class="hero full-width">
	<h1 class="hero__title">Willkommen bei CreateRawVision</h1>
	<p class="hero__subtitle">Deine Anlaufstelle für Rohkost</p>
</header>

<section class="overview" aria-label="Übersicht">
	<p class="overview__item">
		Hier bei CreateRawVision zeigen wir dir, wie du köstliche und
		unvergleichlich gesunde Rohkost zubereitest, die andere in Erstaunen
		versetzen wird.
	</p>
	<p class="overview__item">
		Bereits seit 2013 zeige ich Interessierten wie köstliche und gesunde Rohkost
		zubereitet wird. Gesundes, vitalstoffreiches und köstliches Essen muss nicht
		schwer sein und ich zeige dir wie!
	</p>
	<p class="overview__item">
		Für nur 9 Euro im Monat bekommst du uneingeschränkten Zugang zu hunderten
		Rezepten und unzähligen Tutorials. Dadurch kann sich dein ganzes Leben
		verändern.
	</p>
</section>

<section class="introduction">
	<h2 class="introduction__heading">
		Warum solltest du Rohkost in Deine Ernährung Integrieren?
	</h2>
	<ul class="introduction__list">
		<li class="introduction__item">
			Weil du es verdient hast, fit und voller Energie zu sein!
		</li>
		<li class="introduction__item">
			Weil du es verdient hast, vitalstoffreiche Lebensmittel zu dir zu nehmen!
		</li>
		<li class="introduction__item">
			Weil du es verdient hast, gesund zu sein!
		</li>
		<li class="introduction__item">
			Weil du es verdient hast, deine Jugend und Lebensfreude zu bewahren!
		</li>
		<li class="introduction__item">
			Und weil du es verdient hast, absolut köstliches Essen zu verzehren!
		</li>
	</ul>
</section>

<section class="video" aria-label="Video: Warum solltest du Rohkost in deine Ernährung einbauen?">
	<?php
	echo wp_video_shortcode(
		array(
			'src'    => '/wp-content/uploads/2020/08/warum-sollest-du-rohkost-in-deine-ernaehrung-einbauen.mp4',
			'height' => 720,
			'width'  => 1280,
			'poster' => '',
		)
	);
	?>
</section>

<!-- @todo Bilder -->
<section class="offer-summary full-width">
	<h2 class="offer-summary__heading">
		In Meinem Mitgliederbereich kannst Du Erfolgreich mit der Rohkost in ein
		Gesundes Leben Starten!
	</h2>
	<ul class="offer-summary__list">
		<li class="offer-summary__item">Erprobte Rohkost-Rezepte</li>
		<li class="offer-summary__item">Einfache Tutorials</li>
		<li class="offer-summary__item">Verständlicher Kurs für Anfänger</li>
	</ul>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt mit Rohkost durchstarten!
	</button>
</a>

<section class="testimonials">
	<h2 class="testimonials__title">
		Das sagen unsere Nutzer
	</h2>
	<div class="testimonials__popup-wrapper">
	<ul class="testimonials__list">
		<!-- @todo content -->
		<li class="testimonials__entry">
			<img class="testimonials__portrait" src="https://picsum.photos/200" />
			<div class="testimonials__content">
				<h3 class="testimonials__name">Vorname1 Name1</h3>
				<span class="testimonials__address">aus Stadt</span>
				<p class="testimonials__text">
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
				</p>
			</div>
		</li>
		<li class="testimonials__entry">
			<img class="testimonials__portrait" src="https://picsum.photos/200" />
			<div class="testimonials__content">
				<h3 class="testimonials__name">Vorname2 Name2</h3>
				<span class="testimonials__address">aus Stadt</span>
				<p class="testimonials__text">
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
				</p>
			</div>
		</li>
		<li class="testimonials__entry">
			<img class="testimonials__portrait" src="https://picsum.photos/200" />
			<div class="testimonials__content">
				<h3 class="testimonials__name">Vorname3 Name3</h3>
				<span class="testimonials__address">aus Stadt</span>
				<p class="testimonials__text">
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
					Das hab ich zu sagen. Da gibt's so einiges, was ich zu sagen habe...
					Also, das ist meine Message. Hör genau her!
				</p>
			</div>
		</li>
	</ul>
	<div class="testimonials__popup"></div>
	</div>
</section>

<section class="offer">
	<h2 class="offer__title">
		Was Bietet Dir der Mitgliederbereich?
	</h2>
	<p class="offer__intro">
		Deine Mitgliedschaft beinhaltet exklusiven und unbegrenzten Zugriff auf:
	</p>
	<ul class="offer__list">
		<li class="offer__item">
			Über 500 exklusive, gesunde, vitalstoffreiche, köstliche und getestete
			rohe und vegane Rezepte (Link zu Membershiprezepten) Diese Rezepte passen
			hervorragend zu vielen gesunden Ernährungsrichtungen. Das beinhaltet
			Rohkost, vegan, vegetarisch, milchfrei, eifrei, sojafrei, glutenfrei und
			noch vieles darüber hinaus.
		</li>
		<li class="offer__item">
			Verschiedene Zubereitungstechniken mit denen du deine Rezepte so
			zubereiten kannst, dass sie köstlich schmecken und dennoch alle wertvollen
			Vitalstoffe erhalten bleiben. Das beinhaltet das Zubereiten von Speisen
			mit einer ähnlichen Textur und ähnlichem Geschmack wie gekochte Speisen.
		</li>
		<li class="offer__item">
			Druckbare PDF-Rezepte – Erstelle dein eigenes Rohkost Kochbuch
		</li>
		<li class="offer__item">
			Monatliche Gruppen-Lifecalls mit uns, in denen wir dir deine Fragen
			beantworten.
		</li>
		<li class="offer__item">
			Monatlich neue großartige Rezepte und Tutorials die wir regelmäßig auf
			unserer und deiner Reise kreieren.
		</li>
		<li class="offer__item">
			Eine große Vielfalt an Schritt-für-Schritt-Anleitungen, Infos und Tipps
		</li>
		<li class="offer__item">
			Wunschbox – Bei uns kannst du Rezeptwünsche abgeben. Wir bemühen dann
			deine Rezeptwünsche in Rohkostqualiät umzusetzen.
		</li>
		<li class="offer__item">
			Auf individuelle Rezeptlisten, die du selber gestalten und mit deinen
			Lieblingsrezepten füllen kannst.
		</li>
	</ul>
</section>

<section class="checkmarks full-width">
	<ul class="checkmarks__list">
		<li class="checkmarks__item">
			<h3 class="checkmarks__heading">500+ Rezepte</h3>
			<p class="checkmarks__text">
				Du bekommst exklusiven Zugriff auf außergewöhnliche Rezepte für
				internationale Gerichte, Kuchen, Brote, Käse und vieles mehr!
			</p>
		</li>
		<li class="checkmarks__item">
			<h3 class="checkmarks__heading">50+ Tutorials</h3>
			<p class="checkmarks__text">
				Mit diesen Tutorials gebe ich dir mein Expertenwissen aus 8 Jahren
				Rohkosterfahrung weiter.
			</p>
		</li>
		<li class="checkmarks__item">
			<h3 class="checkmarks__heading">Kurs für Einsteiger</h3>
			<p class="checkmarks__text">
				Neu in der Rohkost? Mit diesem Kurs fällt dir der Anfang besonders
				leicht!
			</p>
		</li>
	</ul>
</section>

<section class="overview">
	<p class="overview__item">
		Ich habe mehr als 1.500 Tage in meiner Küche verbracht und dort mit rohen
		Zutaten, Techniken und Rezepten experimentiert.
	</p>
	<p class="overview__item">
		Alle meine Rezepte sind mehrfach getestet und sehr beliebt. Und das nicht
		nur bei Rohköstlern, sondern auch bei ganz normalen Essern und ganz
		besonders wichtig - bei Kindern.
	</p>
	<p class="overview__item">
		Du musst nicht komplett von Rohkost leben, um in den Genuss der positiven
		Auswirkungen der Rohkost zu kommen.
	</p>
</section>

<section class="mission full-width">
	<h2 class="mission__title">
		Was Wir Dir zum Abschluss noch Unbedingt Sagen Möchten
	</h2>
	<p class="mission__content">
		Es ist unsere Berufung anderen auf dem Weg zu einer gesunden,
		vitalstoffreichen und energetischen Ernährung für ein besseres Leben zu
		helfen. Wir lieben es, verschiedene, köstliche Rohkost Rezepte zu kreieren.
		Mit unserem Mitgliederbereich möchten wir dir dabei helfen, alle wichtigen
		Fertigkeiten im Bereich Rohkost zu lernen, sodass du für dein ganzes Leben
		gesund und voller Vitalität bist.
	</p>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt durchstarten!
	</button>
</a>

<section class="homepage-faqs">
	<h2>Häufig Gestellte Fragen</h2>
	<div class="homepage-faqs__container">
		<?php
		require_once __DIR__ . '/../lib/faqs/faq-functions.php';
		// Show FAQs, but filter them with 'home_faq' post meta.
		jw_enqueue_faq_scripts_styles();
		$heading_level = 3;
		$meta_query    = array(
			array(
				'key'     => 'home_faq',
				'value'   => true,
				'compare' => '=',
				'type'    => 'BINARY',
			),
		);
		jw_display_faqs( $heading_level, $meta_query );
		?>
	</div>
	<button class="homepage-faqs__button">Mehr anzeigen</button>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Melde dich hier an
	</button>
</a>
