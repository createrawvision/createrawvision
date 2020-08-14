<?php
global $rcp_options;
$registration_page_id = $rcp_options['registration_page'];
$recipes_category_id  = 5869;
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
	<p class="countdown__description countdown__hide-on-done">
		bis zur Veröffentlichung des Mitgliederbereichs
	</p>
	<p class="countdown__description countdown__show-on-done">
		Der Mitgliederbereich ist veröffentlicht!
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
		<p class="countdown__description countdown__show-on-done">
			Der Mitgliederbereich ist veröffentlicht!
		</p>
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

<section class="overview full-width" aria-label="Übersicht">
	<img class="bubble bubble--right" src="<?php echo esc_url( CHILD_URL . '/images/home-himbeerbisquittorte.jpg' ); ?>" alt="Himbeerbisquittorte">
	<p class="overview__item full-width-reset">
		Hier bei CreateRawVision zeigen wir dir, wie du köstliche und
		unvergleichlich gesunde Rohkost zubereitest, die andere in Erstaunen
		versetzen wird.
	</p>
	<img class="bubble bubble--left" src="<?php echo esc_url( CHILD_URL . '/images/home-pasta-pomodoro.jpg' ); ?>" alt="Pasta Pomodoro">
	<p class="overview__item full-width-reset">
		Bereits seit 2013 zeige ich Interessierten wie köstliche und gesunde Rohkost
		zubereitet wird. Gesundes, vitalstoffreiches und köstliches Essen muss nicht
		schwer sein und ich zeige dir wie!
	</p>
	<p class="overview__item full-width-reset">
		Für nur 9 Euro im Monat bekommst du uneingeschränkten Zugang zu hunderten
		Rezepten und unzähligen Tutorials. Dadurch kann sich dein ganzes Leben
		verändern.
	</p>
</section>

<section class="video" aria-label="Video: Warum solltest du Rohkost in deine Ernährung einbauen?">
	<?php
	$width  = 1280;
	$height = 720;
	echo wp_video_shortcode(
		array(
			'src'    => wp_get_attachment_url( 20981 ),
			'height' => $height,
			'width'  => $width,
			'poster' => wp_get_attachment_image_url( 22184, array( $width, $height ) ),
		)
	);
	?>
</section>

<section class="introduction">
	<h2 class="introduction__heading">
		Warum solltest du Rohkost in Deine Ernährung Einbauen?
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

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt mit Rohkost durchstarten!
	</button>
</a>

<section class="checkmarks full-width">
	<h2 class="checkmarks__title">
		In Unserem Mitglieder&shy;bereich kannst Du Erfolgreich mit der Rohkost in ein
		Gesundes Leben Starten!
	</h2>
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

<section class="about-us">
	<img class="about-us__image" src="<?php echo esc_url( CHILD_URL . '/images/angie-josef-portrait.png' ); ?>" alt="Portrait Angie & Josef">
	<h2 class="about-us__heading">Hey, ich bin Angie!</h2>
	<p class="about-us__text">Vor etwa sieben Jahren habe ich die Rohkost entdeckt. Dadurch veränderte sich mein Leben in jeder Hinsicht grundlegend. Dank der Rohkost habe ich mich auf eine aufregende, spirituelle und erfüllende Reise begeben, durch die ich mittlerweile über unglaublich viel Energie, wesentlich mehr Gelassenheit und Ruhe verfüge.</p>
	<p class="about-us__text">Doch auch in der Rohkost stellten sich mir viele Hindernisse in den Weg. Vor allem zum Beginn meiner Reise vermisste ich den Geschmack von gekochten Gerichten. Daher war es mir sehr wichtig einen Ersatz für all die Rezepte zu finden, deren Geschmack mir vor der Rohkost so viel Freude bereitet haben. </p>
	<p class="about-us__text">Trotz der vielen Rohkostrezeptbücher war ich mit der Auswahl an Rezepten und deren Qualität nicht zufrieden. Oft gefielen mir nur ein paar wenige Rezepte pro Buch. Daher fing ich schnell an selbst kreativ zu werden. Mittlerweile habe ich mir in diesem Bereich so viel Wissen und Erfahrung angeeignet, dass ich spezielle Techniken gefunden habe, um die leckersten Rezepte der Kochkost rohkosttauglich zu machen.</p>
	<h2 class="about-us__heading">Hallo, ich bin Josef!</h2>
	<p class="about-us__text">Vor vier Jahren habe ich die Rohkost entdeckt. Obwohl ich erst 18 Jahre alt war, hatte ich schon viele kleine Beschwerden wie Tinnitus, schmerzende Gelenke und Bauchschmerzen. Diese sind innerhalb von wenigen Monaten verschwunden, nachdem ich immer mehr gesunde, rohe Nahrung zu mir nahm.</p>
	<p class="about-us__text">Die Umstellung war keinesfalls leicht, vor allem, weil ich von allen Seiten verschiedene Informationen erhalten habe. Meine Mahlzeiten waren oft sehr langweilig und andere hatten auch kaum Verständnis. Aber jetzt kann ich mir ein Leben ohne Rohkost nicht mehr vorstellen.</p>
</section>

<section class="recipe-slider full-width">
	<h2 class="recipe-slider__heading">Schau dir hier einige meiner Rezepte an</h2>
	<?php echo do_shortcode( '[slide-anything id="22150"]' ); ?>
</section>

<a href="<?php echo esc_url( get_category_link( $recipes_category_id ) ); ?>" target="_blank">
	<button class="cta-button">
		Alle Rezepte anschauen
	</button>
</a>

<section class="offer full-width fading-bg fading-bg--right">
	<div class="fading-bg__image"><div class="fading-bg__overlay"></div></div>
	<div class="full-width-reset fading-bg__content offer__container">
		<h2 class="offer__heading">Das bekommst du für nur 9 EUR im Monat</h2>
		<ul class="offer__list">
			<li class="offer__item">500+ vegane Rohkost Rezepte</li>
			<li class="offer__item">50 + Tipps, Tutorials und Schritten Rohkost Leicht Gemacht mit 60+ Lektionen</li>
			<li class="offer__item">Mitgliederbereich mit einfacher Bedienung und kurzen Ladezeiten</li>
			<li class="offer__item">Regelmäßig neue Rezepte, Tipps, Schritt-für-Schritt-Anleitungen</li>
			<li class="offer__item">Aktive Mitgestaltung durch Wunschrezepte</li>
			<li class="offer__item">Erstellen von eigenen Rezeptsammlungen </li>
			<li class="offer__item">Monatliche Live-Calls</li>
			<li class="offer__item">Ständige Pflege, Aktualisierung und Verbesserung des Mitgliederbereiches, der Inhalte und Rezepte</li>
			<li class="offer__item">Rückfrage-Möglichkeiten zu den Rezepten – mit schnellen Antworten</li>
			<li class="offer__item">Mitgliedschaft in einer wachsenden, sich gegenseitig unterstützenden Community</li>
			<li class="offer__item">Gut sortierte Rezepte in verschiedenen Kategorien</li>
			<li class="offer__item">Alle Rohkost-Zubereitungstechniken</li>
			<li class="offer__item">Druckbare Rezepte im PDF-Format</li>
			<li class="offer__item">Empfehlungen für gut recherchierte und erprobte Produkte</li>
		</ul>
	</div>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt anmelden
	</button>
</a>

<section class="imagine full-width fading-bg fading-bg--left">
	<div class="fading-bg__image"><div class="fading-bg__overlay"></div></div>
	<div class="full-width-reset fading-bg__content imagine__container">
		<h2 class="imagine__heading">Träumst Du Davon…</h2>
		<ul class="imagine__list">
			<li class="imagine__item">Den ganzen Tag energiegeladen und kraftvoll zu sein?</li>
			<li class="imagine__item">Einen gesunden Körper zu haben, in dem du dich rundum wohl fühlst und der frei von jeglichen Beschwerden ist?</li>
			<li class="imagine__item">Morgens voller Lebensfreude aufzustehen und den Tag kaum erwarten zu können?</li>
		</ul>
		<h2 class="imagine__heading">Was Wäre Wenn…</h2>
		<ul class="imagine__list">
			<li class="imagine__item">Du in wenigen Wochen deine Ernährung so umstellst, dass dir unbegrenzt Kraft, Energie und Lebensfreude zur Verfügung steht?</li>
			<li class="imagine__item">Du dich wieder fühlen würdest wie ein Kind und das Leben wieder bunt und farbenfroh ist?</li>
			<li class="imagine__item">Dein Körper endlich wieder in dem Zustand ist, wie du es verdient hast und es von der Natur vorgesehen wurde?</li>
		</ul>
	</div>
</section>

<section class="testimonials">
	<h2 class="testimonials__title">
		Das sagen unsere Nutzer
	</h2>
	<?php echo do_shortcode( '[slide-anything id="22167"]' ); ?>
</section>

<section class="overview full-width">
	<img class="bubble bubble--right" src="<?php echo esc_url( CHILD_URL . '/images/home-karottensuppe.jpg' ); ?>" alt="Karottensuppe">
	<p class="overview__item full-width-reset">
		Ich habe mehr als 1.500 Tage in meiner Küche verbracht und dort mit rohen
		Zutaten, Techniken und Rezepten experimentiert.
	</p>
	<img class="bubble bubble--left" src="<?php echo esc_url( CHILD_URL . '/images/home-kirsch-topfenstrudel.jpg' ); ?>" alt="Kirsch-Topfenstrudel">
	<p class="overview__item full-width-reset">
		Alle meine Rezepte sind mehrfach getestet und sehr beliebt. Und das nicht
		nur bei Rohköstlern, sondern auch bei ganz normalen Essern und ganz
		besonders wichtig - bei Kindern.
	</p>
	<p class="overview__item full-width-reset">
		Du musst nicht komplett von Rohkost leben, um in den Genuss der positiven
		Auswirkungen der Rohkost zu kommen.
	</p>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt mit Rohkost durchstarten!
	</button>
</a>

<section class="comparison">
	<h2>Was sind die Vorteile eines Mitglieder&shy;bereiches im Vergleich zu Rezeptbüchern?</h2>
	<table>
		<thead>
			<tr>
				<td></td>
				<th scope="col">Rezeptbuch</th>
				<th scope="col">Mitgliederbereich</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">Menge an Rezepten</th>
				<td>Geringe und begrenzte Anzahl</td>
				<td>500+ Rezepte und ständig wachsende Anzahl; aktuelle und neue Rezepte passend zur Saison</td>
			</tr>
			<tr>
				<th scope="row">Tipps, Anleitungen</th>
				<td>Geringe und begrenzte Anzahl</td>
				<td>50+ Tipps und Anleitungen mit ständig wachsender Anzahl</td>
			</tr>
			<tr>
				<th scope="row">Art der Rezepte</th>
				<td>Vorgegebene Rezepte, keine Möglichkeit der Mitgestaltung, Lieblingsrezepte in mehreren Büchern</td>
				<td>Mitgestaltung der Rezepte durch Wunschbox und individuelle Zusammenstellung der Lieblingsrezepte, druckbare PDF-Rezepte</td>
			</tr>
			<tr>
				<th scope="row">Kontakt</th>
				<td>Kein direkter Kontakt zum Autor möglich, keine Fragen zu Rezepten</td>
				<td>Monatliche Live-Calls mit dem CreateRawVision Team, Kommentare unter den Rezepten mit Antworten und persönlichem Support</td>
			</tr>
			<tr>
				<th scope="row">Verbesserung von Funktionalität, Design und bestehenden Rezepten</th>
				<td>Keine Veränderung bis zur nächsten Buchauflage</td>
				<td>Regelmäßige Verbesserung des Mitgliederbereiches im Hinblick auf Funktionalität, Interaktivität und Optik</td>
			</tr>
			<tr>
				<th scope="row">Kontakt mit Gleichgesinnten</th>
				<td>nicht möglich</td>
				<td>Mit deinem Einverständnis Zugang zur Community</td>
			</tr>
			<tr>
				<th scope="row">Übersicht der Rezepte</th>
				<td>Meist keine Einteilung in übersichtliche Kategorien, Hin- und Herblättern</td>
				<td>Einteilung in übersichtliche und ansprechende Kategorien; Mögliche Anwendung eines Filters</td>
			</tr>
			<tr>
				<th scope="row">Zubereitungstechniken</th>
				<td>Geringe und begrenzte Anzahl</td>
				<td>Höhere und ständige wachsende Anzahl an Techniken – Entwicklung und Weiterbildung neuer Techniken</td>
			</tr>
			<tr>
				<th scope="row">Zutaten und Zubehör</th>
				<td>Eingeschränkte und zum Teil veraltete Produktempfehlungen aus dem Ausland</td>
				<td>Topaktuelle, gut recherchierte und getestete Produkte</td>
			</tr>
		</tbody>
	</table>
</section>

<section class="reason full-width fading-bg fading-bg--right">
	<div class="fading-bg__image"><div class="fading-bg__overlay"></div></div>
	<div class="full-width-reset fading-bg__content reason__container">
		<h2 class="reason__heading">Den Mitgliederbereich brauchst du, wenn ...</h2>
		<ul class="reason__list">
			<li class="reason__item">Du dich wieder jung und voller Energie fühlen willst und über mehr geistige Klarheit verfügen möchtest</li>
			<li class="reason__item">Du Mangelerscheinungen, Krankheiten und Allergien verhindern und vorbeugen möchtest</li>
			<li class="reason__item">Du Dich endlich natürlich ernähren möchtest, neugierig bist, was alles mit der Rohkost möglich ist und du deinen Horizont erweitern möchtest</li>
			<li class="reason__item">Du Teil einer supercoolen und motivierten Rohkost Community sein möchtest</li>
			<li class="reason__item">Du auf ein erfahrenes und langjähriges Rohkost-Expertenteam vertrauen möchtest</li>
			<li class="reason__item">Du die besten, köstlichsten und außergewöhnlichsten Rohkost Rezepte der Welt essen möchtest</li>
			<li class="reason__item">Du Fragen zum Thema Rohkost stellen und Antworten bekommen möchtest</li>
			<li class="reason__item">Du alles Wissen zum Thema Rohkost aus einer Hand bekommen und die Rohkostbewegung aktiv mitgestalten möchtest</li>
			<li class="reason__item">Du ein Profi in der Zubereitung von einzigartigen Rohkost Rezepten werden möchtest</li>
			<li class="reason__item">Du all deine Lieblings-Rezepte an einem Ort haben möchtest</li>
		</ul>
	</div>
</section>

<section class="testimonials">
	<h2 class="testimonials__title">
		Das sagen unsere Nutzer
	</h2>
	<?php echo do_shortcode( '[slide-anything id="22172"]' ); ?>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt anmelden
	</button>
</a>

<section class="offer2 full-width fading-bg fading-bg--left">
	<div class="fading-bg__image"><div class="fading-bg__overlay"></div></div>
	<div class="full-width-reset fading-bg__content offer2__container">
		<h2 class="offer2__heading">
			Was Bietet Dir der Mitgliederbereich?
		</h2>
		<p class="offer2__intro">
			Deine Mitgliedschaft beinhaltet exklusiven und unbegrenzten Zugriff auf:
		</p>
		<ul class="offer2__list">
			<li class="offer2__item">
				Über 500 exklusive, gesunde, vitalstoffreiche, köstliche und getestete
				rohe und vegane Rezepte (Link zu Membershiprezepten) Diese Rezepte passen
				hervorragend zu vielen gesunden Ernährungsrichtungen. Das beinhaltet
				Rohkost, vegan, vegetarisch, milchfrei, eifrei, sojafrei, glutenfrei und
				noch vieles darüber hinaus.
			</li>
			<li class="offer2__item">
				Verschiedene Zubereitungstechniken mit denen du deine Rezepte so
				zubereiten kannst, dass sie köstlich schmecken und dennoch alle wertvollen
				Vitalstoffe erhalten bleiben. Das beinhaltet das Zubereiten von Speisen
				mit einer ähnlichen Textur und ähnlichem Geschmack wie gekochte Speisen.
			</li>
			<li class="offer2__item">
				Druckbare PDF-Rezepte – Erstelle dein eigenes Rohkost Kochbuch
			</li>
			<li class="offer2__item">
				Monatliche Gruppen-Lifecalls mit uns, in denen wir dir deine Fragen
				beantworten.
			</li>
			<li class="offer2__item">
				Monatlich neue großartige Rezepte und Tutorials die wir regelmäßig auf
				unserer und deiner Reise kreieren.
			</li>
			<li class="offer2__item">
				Eine große Vielfalt an Schritt-für-Schritt-Anleitungen, Infos und Tipps
			</li>
			<li class="offer2__item">
				Wunschbox – Bei uns kannst du Rezeptwünsche abgeben. Wir bemühen dann
				deine Rezeptwünsche in Rohkostqualiät umzusetzen.
			</li>
			<li class="offer2__item">
				Auf individuelle Rezeptlisten, die du selber gestalten und mit deinen
				Lieblingsrezepten füllen kannst.
			</li>
		</ul>
	</div>
</section>

<section class="choice">
	<h2 class="choice__heading">Du Hast Jetzt Genau Zwei Möglichkeiten:</h2>
	<ol class="choice__list">
		<li class="choice__item">Entweder du machst genauso weiter wie bisher, versorgst deinen Körper minderwertig und bleibst in deiner Situation stecken.</li>
		<li class="choice__item">Oder du kommst mit dem Mitgliederbereich  schnell zu körperlichem Wohlbefinden, Leistungsfähigkeit und Gelassenheit.</li>
	</ol>
</section>

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt mit Rohkost durchstarten!
	</button>
</a>

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
		Jetzt mit Rohkost durchstarten!
	</button>
</a>
