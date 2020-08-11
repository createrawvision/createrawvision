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

<!-- @todo Schrift größer -->
<header class="hero full-width">
	<h1 class="hero__title">Willkommen bei CreateRawVision</h1>
	<p class="hero__subtitle">Deine Anlaufstelle für Rohkost</p>
</header>

<!-- @todo add circle recipe images -->
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

<!-- @todo add thumbnail from 1:05 with video title -->
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

<!-- @todo Bilder -->
<section class="checkmarks full-width">
	<h2 class="checkmarks__title">
		In Meinem Mitgliederbereich kannst Du Erfolgreich mit der Rohkost in ein
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

<a href="<?php the_permalink( $registration_page_id ); ?>">
	<button class="cta-button">
		Jetzt mit Rohkost durchstarten!
	</button>
</a>

<!-- @todo Bilder -->
<section class="about-us">
	<h2>Über uns</h2>
	<h3>Über Mich, Angie</h3>
	<p>Vor etwa sieben Jahren habe ich die Rohkost entdeckt. Dadurch veränderte sich mein Leben in jeder Hinsicht grundlegend. Dank der Rohkost habe ich mich auf eine aufregende, spirituelle und erfüllende Reise begeben, durch die ich mittlerweile über unglaublich viel Energie, wesentlich mehr Gelassenheit und Ruhe verfüge.</p>
	<p>Doch auch in der Rohkost stellten sich mir viele Hindernisse in den Weg. Vor allem zum Beginn meiner Reise vermisste ich den Geschmack von gekochten Gerichten. Daher war es mir sehr wichtig einen Ersatz für all die Rezepte zu finden, deren Geschmack mir vor der Rohkost so viel Freude bereitet haben. </p>
	<p>Trotz der vielen Rohkostrezeptbücher war ich mit der Auswahl an Rezepten und deren Qualität nicht zufrieden. Oft gefielen mir nur ein paar wenige Rezepte pro Buch. Daher fing ich schnell an selbst kreativ zu werden. Mittlerweile habe ich mir in diesem Bereich so viel Wissen und Erfahrung angeeignet, dass ich spezielle Techniken gefunden habe, um die leckersten Rezepte der Kochkost rohkosttauglich zu machen.</p>
	<h3>Über Mich, Josef</h3>
</section>

<section class="recipe-slider">
	<h2>Rezeptslider</h2>
</section>

<!-- @todo links Bild, text rechts. Gemüsetaccos mit Kohlgemüse8: fade into white -->
<section class="offer">
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
</section>

<!-- @todo mit Bild von Pose, text links bild rechts -->
<section class="imagine">
	<h2 class="imagine__heading">Träumst Du Davon…</h2>
	<ul>
		<li class="imagine__item">Den ganzen Tag energiegeladen und kraftvoll zu sein?</li>
		<li class="imagine__item">Einen gesunden Körper zu haben, in dem du dich rundum wohl fühlst und der frei von jeglichen Beschwerden ist?</li>
		<li class="imagine__item">Morgens voller Lebensfreude aufzustehen und den Tag kaum erwarten zu können?</li>
	</ul>
	<h2 class="imagine__heading">Was Wäre Wenn…</h2>
	<ul>
		<li class="imagine__item">Du in wenigen Wochen deine Ernährung so umstellst, dass dir unbegrenzt Kraft, Energie und Lebensfreude zur Verfügung steht?</li>
		<li class="imagine__item">Du dich wieder fühlen würdest wie ein Kind und das Leben wieder bunt und farbenfroh ist?</li>
		<li class="imagine__item">Dein Körper endlich wieder in dem Zustand ist, wie du es verdient hast und es von der Natur vorgesehen wurde?</li>
	</ul>
</section>

<!-- @todo content / slide -->
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

<section class="comparison">
	<h2>Was sind die Vorteile eines Mitgliederbereiches im Vergleich zu Rezeptbüchern?</h2>
	<table>
		<thead>
			<tr>
				<th>&nbsp;</th>
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

<section class="reason">
	<h3 class="reason__heading">Den Mitgliederbereich brauchst du, wenn ...</h3>
	<ul>
		<li class="reason__item">Du dich wieder jung und voller Energie fühlen willst und über mehr geistige Klarheit verfügen möchtest</li>
		<li class="reason__item">Du Mangelerscheinungen, Krankheiten und Allergien verhindern und vorbeugen möchtest</li>
		<li class="reason__item">Du Dich endlich natürlich ernähren möchtest, neugierig bist, was alles mit der Rohkost möglich ist und du deinen Horizont erweitern möchtest</li>
		<li class="reason__item">Du Teil einer supercoolen und motivierten Rohkost Community sein möchtest</li>
		<li class="reason__item">Du auf ein erfahrenes und langjähriges Rohkost-Expertenteam vertrauen möchtest</li>
		<li class="reason__item">Du die besten, köstlichsten und außergewöhnlichsten Rohkost Rezepte der Welt essen möchtest</li>
		<li class="reason__item">Du Fragen zum Thema Rohkost stellen und Antworten bekommen möchtest</li>
		<li class="reason__item">Du alles Wissen zum Thema Rohkost aus einer Hand bekommen und die Rohkostbewegung aktiv mitgestalten möchtest</li>
		<li class="reason__item">Du ein Profi in der Zubereitung von einzigartigen Rohkost Rezepten werden möchtest</li>
		<li class="reason__item">Du all deine Lieblings-Rezepte an einem Ort haben möchtest</li>
	</ul>
</section>

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

<section class="choice">
	<h2 class="choice__heading">Du Hast Jetzt Genau 2 Möglichkeiten:</h2>
	<p class="choice__item">1. Entweder du machst genauso weiter wie bisher, versorgst deinen Körper minderwertig und bleibst in deiner Situation stecken.</p>
	<p class="choice__item">2. Oder du kommst mit dem Mitgliederbereich  schnell zu körperlichem Wohlbefinden, Leistungsfähigkeit und Gelassenheit.</p>
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
