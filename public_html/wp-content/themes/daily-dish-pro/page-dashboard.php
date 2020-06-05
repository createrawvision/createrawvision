<?php

add_action('the_content', 'crv_show_dashboard');

function crv_show_dashboard()
{
  ob_start();
  crv_dashboard_show_recipes();
  crv_dashboard_show_support();
  crv_dashboard_show_community();
  crv_dashboard_show_courses();
  crv_dashboard_show_further();
  return ob_get_clean();
}

function crv_dashboard_show_recipes()
{ ?>
  <h2>Rezepte</h2>
  <ul>
    <li>Rezepte nach Kategorien</li>
    <li>Rezepte suchen</li>
    <li>Neue Rezepte</li>
    <li>Deine Lieblingsrezepte</li>
    <li>Beliebte Rezepte</li>
  </ul>
<?php }

function crv_dashboard_show_support()
{ ?>
  <h2>Support</h2>
  <ul>
    <li><a href="/faqs">Häufige Fragen</a></li>
    <li><a href="/kontaktformular">Kontakt</a></li>
    <li>Suche <?php get_search_form(); ?></li>
  </ul>
<?php }

function crv_dashboard_show_community()
{ ?>
  <h2>Community (demnächst verfügbar)</h2>
<?php }

function crv_dashboard_show_courses()
{ ?>
  <h2>Kurse</h2>

<?php }

function crv_dashboard_show_further()
{ ?>
  <h2>Weiteres</h2>
<?php }

genesis();
