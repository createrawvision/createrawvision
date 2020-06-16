<?php

namespace crv\dashboard;

add_action('the_content', __NAMESPACE__ . '\show_dashboard');

function show_dashboard()
{
  ob_start();
  echo rcp_restricted_message_pending_verification(''); ?>
  <h2><a href="<?php echo get_category_link(4269); ?>">Mitgliederbereich-Übersicht</a></h2>
  <?php
  show_recipes();
  show_tutorials(); ?>
  <h2><a href="<?php echo get_category_link(5792); ?>">Dein Weg Zur Rohkost Leicht Gemacht - Kurs</a></h2>
<?php
  show_community();
  show_blog();
  show_further();
  show_admin();
  show_support();
  return ob_get_clean();
}

function show_recipes()
{ ?>
  <h2>Rezepte</h2>
  <ul>
    <li><a href="<?php echo esc_url(get_term_link('rezepte', 'category')); ?>">Rezepte nach Kategorien</a></li>
    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('suche'))); ?>">Rezepte suchen</a></li>
    <!-- @todo -->
    <li>Neue Rezepte</li>
    <!-- @todo -->
    <li>Deine Lieblingsrezepte</li>
    <!-- @todo -->
    <li>Beliebte Rezepte</li>
  </ul>
<?php }

function show_support()
{ ?>
  <h2>Support</h2>
  <ul>
    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('faqs'))); ?>">Häufige Fragen</a></li>
    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('kontaktformular'))); ?>">Kontakt</a></li>
    <!-- @todo -->
    <li><?php get_search_form(); ?></li>
  </ul>
<?php }

function show_community()
{ ?>
  <!-- @todo -->
  <h2>Community (demnächst verfügbar)</h2>
<?php }

function show_tutorials()
{
  $course_category_id = get_category_by_slug('tutorials')->term_id;
  $courses = get_categories(['parent' => $course_category_id]);
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

/** @todo add main blog page  */
function show_blog()
{
  $blog_category_id = get_category_by_slug('blog')->term_id;
  $categories = get_categories(['parent' => $blog_category_id]);
?>
  <h2>Blog</h2>
  <ul>
    <?php
    foreach ($categories as $category) {
      echo '<li><a href="' . get_category_link($category) . '">' . $category->name . '</a></li>';
    } ?>
  </ul>
<?php }

function show_further()
{ ?>
  <h2>Weiteres</h2>
  <ul>
    <!-- @todo -->
    <li>Q&As</li>
    <!-- @todo -->
    <li>Kommende Veranstaltungen</li>
    <!-- @todo -->
    <li>Unsere Vision</li>
  </ul>
<?php }

function show_admin()
{
  global $rcp_options; ?>
  <h2>Einstellungen</h2>
  <ul>
    <li><a href="<?php the_permalink($rcp_options['edit_profile']); ?>">Profil bearbeiten</a></li>
    <li><a href="<?php the_permalink($rcp_options['account_page']); ?>">Mitgliedschaft verwalten</a></li>
  </ul>
<?php }


genesis();
