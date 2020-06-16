<?php

namespace crv\dashboard;

add_action('the_content', __NAMESPACE__ . '\show_dashboard');

function show_dashboard()
{
  ob_start();
  echo rcp_restricted_message_pending_verification('');
  show_recipes();
  show_support();
  show_community();
  show_courses();
  show_further();
  show_admin();
  return ob_get_clean();
}

function show_recipes()
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

function show_support()
{ ?>
  <h2>Support</h2>
  <ul>
    <li><a href="/faqs">Häufige Fragen</a></li>
    <li><a href="/kontaktformular">Kontakt</a></li>
    <li>Suche <?php get_search_form(); ?></li>
  </ul>
<?php }

function show_community()
{ ?>
  <h2>Community (demnächst verfügbar)</h2>
<?php }

function show_courses()
{
  $course_category_id = get_category_by_slug('tutorials')->term_id;
  $courses = get_categories(['parent' => $course_category_id])
?>
  <h2>Tutorials</h2>
  <ul>
    <?php
    foreach ($courses as $course) {
      echo '<li><a href="' . get_category_link($course) . '">' . $course->name . '</a></li>';
    } ?>
  </ul>
<?php
}

function show_further()
{ ?>
  <h2>Weiteres</h2>
  <ul>
    <li>Blog</li>
    <li>Q&As</li>
    <li>Kommende Veranstaltungen</li>
    <li>Unsere Vision</li>
  </ul>
<?php }

function show_admin()
{
  global $rcp_options; ?>
  <h2>Einstellungen</h2>
  <ul>
    <li><a href="<?php echo get_permalink($rcp_options['edit_profile']); ?>">Profil bearbeiten</a></li>
    <li><a href="<?php echo get_permalink($rcp_options['account_page']); ?>">Mitgliedschaft verwalten</a></li>
  </ul>
<?php }


genesis();
