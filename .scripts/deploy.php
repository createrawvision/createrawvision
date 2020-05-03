<?php

/**
 * Makes changes to the database by comparing 'crv_version' option.
 * 
 * Execute with WP-CLI (and add `--user=Josef` to have enough capabilites)!
 */

if (!defined('WP_CLI') || !WP_CLI) {
  echo "WP_CLI not defined", PHP_EOL;
  exit(1);
}

if (!current_user_can('manage_options')) {
  echo "Insufficient capabilites. Make sure to run the script with admin capabilites (e.g. --user=<admin>).", PHP_EOL;
  exit(1);
}

/**
 * Run a WP_CLI command.
 * 
 * Run `wp cli has_command` before executing the command.   
 * Only exits, if the command couldn't be executed and `exit_error` is set to exit.  
 * `$options['return']` can be `'return_code'`, `'stdout'`, `'stderr'` or `'all'`
 */
function run_wp_cli_command($command, $options = [])
{
  $default_options = [
    'launch' => false,
    'exit_error' => false,
    'return' => 'return_code'
  ];

  $options = array_merge($default_options, $options);

  if (wp_cli_has_command($command)) {
    return WP_CLI::runcommand($command, $options);
  } else {
    $message = "Couldn't find command \"${command}\"";
    if ($options['exit_error']) {
      WP_CLI::error($message);
    } else {
      WP_CLI::warning($message);
    }
  }
}

/**
 * Checks if WP-CLI knows the command.
 * 
 * Escapes quotes by running `addslahes` on the input.
 */
function wp_cli_has_command($command)
{
  $command = addslashes($command);
  $return_code = WP_CLI::runcommand("cli has-command \"${command}\"", array(
    'launch' => false,
    'return'      => 'return_code',
    'exit_error'  => false
  ));
  return $return_code == 0;
}

// Avoid the output buffer
ob_end_flush();
ob_implicit_flush();

// Check the version to deploy all needed changes
$version_option_name = 'crv_version';
$version = get_option($version_option_name);

if (!$version) {
  $version = '0.0.0';
  add_option($version_option_name, $version);
}

$new_version = '0.1.0';
if (version_compare($version, $new_version, '<')) {
  WP_CLI::log("Deploying version $new_version");

  /**
   * Setup for category featured images
   */
  WP_CLI::log("Installing and activating Advanced Custom Fields");
  run_wp_cli_command("plugin install advanced-custom-fields --activate --force", ['exit_error' => true]);

  WP_CLI::log("Removing categories from posts, when category has child categories");

  $childless_category_ids = get_categories([
    'childless' => true,
    'hide_empty' => false,
    'fields' => 'ids'
  ]);
  $all_category_ids = get_categories([
    'hide_empty' => false,
    'fields' => 'ids'
  ]);
  $parent_category_ids = array_values(array_diff(
    $all_category_ids,
    $childless_category_ids
  ));

  $post_ids_to_edit = get_posts([
    'category__in' => $parent_category_ids,
    'fields' => 'ids',
    'posts_per_page' => -1,
    'post_status' => 'any'
  ]);

  foreach ($post_ids_to_edit as $post_id) {
    wp_remove_object_terms($post_id, $parent_category_ids, 'category');
  }
  WP_CLI::log("Removed parent categories from following posts: " . (implode(',', $post_ids_to_edit) ?: '(none)'));

  WP_CLI::log('Setting category featured image from JSON data');
  $category_images_json = file_get_contents(ABSPATH . '../deployment_data/category-images.json');
  $category_images = json_decode($category_images_json, $assoc = TRUE);
  foreach ($category_images as ['term_name' => $term_name, 'term_id' => $term_id, 'image_title' => $image_title]) {

    // Get the image_id by title, skip if not found
    $images = get_posts(['post_type' => 'attachment', 'title' => $image_title, 'post_status' => null, 'numberposts' => 1]);
    if (!$images) {
      WP_CLI::warning("Couldn't find image with title ${image_title}. Skipping...");
      continue;
    }
    $image_id = $images[0]->ID;

    // get term_id by id, then by name, skip if not found
    $term = get_term_by('id', $term_id, 'category');
    if (!$term) {
      WP_CLI::warning("Couldn't find term with id ${term_id}. Skipping...");
      $term = get_term_by('name', $term_name, 'category');
    }
    if (!$term) {
      WP_CLI::warning("Couldn't find term with name ${term_name}. Trying id...");
      continue;
    }
    $term_id = $term->term_id;

    $success = acf_save_post('term_' . $term_id, ['field_1' => $image_id]);

    if (!$success) {
      WP_CLI::warning("Couldn't add image $image_title to term $term->name. Skipping...");
    }
  }


  /**
   * Restrict Content Pro setup
   * 
   * @todo Restrict site content (a2kA1_termmeta -> meta_key = rcp_restricted_meta)
   */
  WP_CLI::log("Installing and activating Restrict Content Pro");
  $rcp_path = ABSPATH . '../deployment_data/restrict-content-pro.zip';
  run_wp_cli_command("plugin install '$rcp_path' --activate --force", ['exit_error' => true]);

  WP_CLI::log("Setting RCP Settings");
  $rcp_settings = array(
    'auto_renew' => '1',
    'currency' => 'EUR',
    'currency_position' => 'before',
    'gateways' => array('digistore' => '1'),
    'email_template' => 'default',
    'email_header_text' => 'Hallo',
    'email_header_img' => '',
    'from_name' => 'CreateRawVision',
    'from_email' => 'info@createrawvision.de',
    'admin_notice_emails' => 'info@createrawvision.de'
  );
  $current_rcp_settings = get_option('rcp_settings');
  $new_rcp_settings = wp_parse_args($rcp_settings, $current_rcp_settings);
  update_option('rcp_settings', $new_rcp_settings);

  WP_CLI::warning("RCP license key wasn't set. Add the license key manually.");

  WP_CLI::log("Creating membership levels");
  $rcp_levels = new RCP_Levels();
  $levels_args = array(
    array(
      'name' => 'CreateRawVision Member (monatlich)',
      'description' => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
      'duration' => '1',
      'duration_unit' => 'month',
      'trial_duration' => '0',
      'trial_duration_unit' => 'day',
      'price' => '10',
      'fee' => '0',
      'maximum_renewals' => '0',
      'after_final_payment' => '',
      'list_order' => '0',
      'level' => '0',
      'status' => 'active',
      'role' => 'subscriber',
    ),
    array(
      'name' => 'CreateRawVision Member (jährlich)',
      'description' => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
      'duration' => '1',
      'duration_unit' => 'year',
      'trial_duration' => '0',
      'trial_duration_unit' => 'day',
      'price' => '80',
      'fee' => '0',
      'maximum_renewals' => '0',
      'after_final_payment' => '',
      'list_order' => '0',
      'level' => '0',
      'status' => 'active',
      'role' => 'subscriber',
    )
  );
  foreach ($levels_args as $level_args) {
    if ($rcp_levels->get_level_by('name', $level_args['name'])) {
      WP_CLI::warning("Membership Level '${level_args['name']}' already exists. Not changing it.");
    } else {
      $rcp_levels->insert($level_args);
    }
  }


  /**
   * Setup for support/faq page
   */
  WP_CLI::log("Creating FAQs page, if not already existing");
  $faqs_title = 'Häufig gestellte Fragen';
  $faqs_name = 'faqs';
  if (!get_posts([
    'name' => $faqs_name,
    'post_type' => 'page',
    'post_status' => 'publish',
    'numberposts' => 1
  ])) {
    wp_insert_post([
      'post_title' => $faqs_title,
      'post_name' => $faqs_name,
      'post_type' => 'page',
      'post_status' => 'publish'
    ]);
  }

  WP_CLI::log("Creating all FAQs");

  // Create FAQs from JSON file
  $faqs_json = file_get_contents(ABSPATH . '../deployment_data/faqs.json');
  $category_objs = json_decode($faqs_json);
  foreach ($category_objs as $category_obj) {
    $category = $category_obj->category;

    // Create the category term
    $term = wp_insert_term(
      $category->name,
      'faq_category',
      ['slug' => $category->slug]
    );
    if (is_wp_error($term)) {
      WP_CLI::warning($term->get_error_message());
      continue;
    }
    ['term_id' => $term_id] = $term;

    // Create all faqs
    $faqs = $category_obj->faqs;
    foreach ($faqs as $faq) {
      wp_insert_post([
        'post_title' => $faq->title,
        'post_content' => $faq->content,
        'post_status' => 'publish',
        'post_type' => 'faq',
        'tax_input' => ['faq_category' => [$term_id]]
      ]);
    }
  }

  // Create Category to collect uncategorized FAQs
  wp_insert_term(
    'Sonstige Fragen',
    'faq_category',
    ['slug' => 'other-faqs']
  );



  /**
   * Create new nav menus
   */
  // Delete existing menus
  run_wp_cli_command('menu delete "Main Menu 2020"');
  run_wp_cli_command('menu delete "Main Menu Member 2020"');
  run_wp_cli_command('menu delete "Secondary Menu 2020"');

  // Create new menus
  $main_menu_id = run_wp_cli_command('menu create "Main Menu 2020" --porcelain', ['return' => 'stdout']);
  $main_menu_member_id = run_wp_cli_command('menu create "Main Menu Member 2020" --porcelain', ['return' => 'stdout']);
  $secondary_menu_id = run_wp_cli_command('menu create "Secondary Menu 2020" --porcelain', ['return' => 'stdout']);

  // Create main menu items
  $entry_menu_item_id = run_wp_cli_command("menu item add-custom $main_menu_id 'Neu hier?' '' --porcelain", ['return' => 'stdout']);
  run_wp_cli_command("menu item add-custom $main_menu_id 'Über uns' '' --parent-id=$entry_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_id 'Unsere Vision' '' --parent-id=$entry_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_id 'Häufige Fragen' '' --parent-id=$entry_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_id 'Beste Beiträge' '' --parent-id=$entry_menu_item_id");

  run_wp_cli_command("menu item add-custom $main_menu_id 'Rohkost Rezepte' ''");
  run_wp_cli_command("menu item add-custom $main_menu_id 'How Tos' ''");

  $blog_menu_item_id = run_wp_cli_command("menu item add-custom $main_menu_id 'Blog' '' --porcelain", ['return' => 'stdout']);
  run_wp_cli_command("menu item add-custom $main_menu_id 'Bewusstsein & Achtsamkeit' '' --parent-id=$blog_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_id 'Gesund Leben' '' --parent-id=$blog_menu_item_id");

  run_wp_cli_command("menu item add-custom $main_menu_id 'Bücher' ''");
  run_wp_cli_command("menu item add-custom $main_menu_id 'Empfehlungen' ''");

  // Create main menu member items
  $entry_menu_item_id = run_wp_cli_command("menu item add-custom $main_menu_member_id 'Neu hier?' '' --porcelain", ['return' => 'stdout']);
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Über uns' '' --parent-id=$entry_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Unsere Vision' '' --parent-id=$entry_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Häufige Fragen' '' --parent-id=$entry_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Beste Beiträge' '' --parent-id=$entry_menu_item_id");

  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Rohkost Rezepte' ''");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'How Tos' ''");

  $blog_menu_item_id = run_wp_cli_command("menu item add-custom $main_menu_member_id 'Blog' '' --porcelain", ['return' => 'stdout']);
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Bewusstsein & Achtsamkeit' '' --parent-id=$blog_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Gesund Leben' '' --parent-id=$blog_menu_item_id");

  $community_menu_item_id = run_wp_cli_command("menu item add-custom $main_menu_member_id 'Community' '' --porcelain", ['return' => 'stdout']);
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Forum' '' --parent-id=$community_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Q&A' '' --parent-id=$community_menu_item_id");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Veranstaltungen' '' --parent-id=$community_menu_item_id");

  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Empfehlungen' ''");

  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Account' ''");
  run_wp_cli_command("menu item add-custom $main_menu_member_id 'Dashboard' ''");

  // Create secondary menu items
  run_wp_cli_command("menu item add-custom $secondary_menu_id 'Kontakt & Coaching' '' --porcelain");

  $work_with_me_menu_item_id = run_wp_cli_command("menu item add-custom $secondary_menu_id 'Arbeite mit mir' '' --porcelain", ['return' => 'stdout']);
  run_wp_cli_command("menu item add-custom $secondary_menu_id 'Workshops' '' --parent-id=$work_with_me_menu_item_id");
  run_wp_cli_command("menu item add-custom $secondary_menu_id 'Kooperationen' '' --parent-id=$work_with_me_menu_item_id");
  run_wp_cli_command("menu item add-custom $secondary_menu_id 'Rezeptentwicklung' '' --parent-id=$work_with_me_menu_item_id");

  $facebook_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M23.738.214v4.714h-2.804c-1.023 0-1.714.214-2.071.643s-.536 1.071-.536 1.929v3.375h5.232l-.696 5.286h-4.536v13.554h-5.464V16.161H8.309v-5.286h4.554V6.982c0-2.214.62-3.932 1.857-5.152S17.607 0 19.666 0c1.75 0 3.107.071 4.071.214z"/></svg>';
  $pinterest_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4.571 10.661q0-1.929.67-3.634t1.848-2.973 2.714-2.196T13.107.465t3.607-.464q2.821 0 5.25 1.188t3.946 3.455 1.518 5.125q0 1.714-.339 3.357t-1.071 3.161-1.786 2.67-2.589 1.839-3.375.688q-1.214 0-2.411-.571t-1.714-1.571q-.179.696-.5 2.009t-.42 1.696-.366 1.268-.464 1.268-.571 1.116-.821 1.384-1.107 1.545l-.25.089-.161-.179q-.268-2.804-.268-3.357 0-1.643.384-3.688t1.188-5.134.929-3.625q-.571-1.161-.571-3.018 0-1.482.929-2.786t2.357-1.304q1.089 0 1.696.723t.607 1.83q0 1.179-.786 3.411t-.786 3.339q0 1.125.804 1.866t1.946.741q.982 0 1.821-.446t1.402-1.214 1-1.696.679-1.973.357-1.982.116-1.777q0-3.089-1.955-4.813t-5.098-1.723q-3.571 0-5.964 2.313t-2.393 5.866q0 .786.223 1.518t.482 1.161.482.813.223.545q0 .5-.268 1.304t-.661.804q-.036 0-.304-.054-.911-.268-1.616-1t-1.089-1.688-.58-1.929-.196-1.902z"/></svg>';
  $youtube_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M29.7 10.3s-.3-2-1.1-2.8c-1.1-1.1-2.3-1.1-2.8-1.2C21.9 6 16 6 16 6s-5.9 0-9.8.3c-.6.1-1.7.1-2.8 1.2-.8.9-1.1 2.8-1.1 2.8S2 12.6 2 14.9v2.2c0 2.3.3 4.6.3 4.6s.3 2 1.1 2.8c1.1 1.1 2.5 1.1 3.1 1.2 2.2.2 9.5.3 9.5.3s5.9 0 9.8-.3c.5-.1 1.7-.1 2.8-1.2.8-.9 1.1-2.8 1.1-2.8s.3-2.3.3-4.6v-2.2c0-2.3-.3-4.6-.3-4.6zm-16.6 9.4v-8l7.6 4-7.6 4z"/></svg>';
  $twitter_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M30.071 7.286q-1.196 1.75-2.893 2.982.018.25.018.75 0 2.321-.679 4.634t-2.063 4.437-3.295 3.759-4.607 2.607-5.768.973q-4.839 0-8.857-2.589.625.071 1.393.071 4.018 0 7.161-2.464-1.875-.036-3.357-1.152t-2.036-2.848q.589.089 1.089.089.768 0 1.518-.196-2-.411-3.313-1.991t-1.313-3.67v-.071q1.214.679 2.607.732-1.179-.786-1.875-2.054t-.696-2.75q0-1.571.786-2.911Q6.052 8.285 9.15 9.883t6.634 1.777q-.143-.679-.143-1.321 0-2.393 1.688-4.08t4.08-1.688q2.5 0 4.214 1.821 1.946-.375 3.661-1.393-.661 2.054-2.536 3.179 1.661-.179 3.321-.893z"/></svg>';
  $instagram_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M29.448 15.936c0 2.661-.029 4.502-.087 5.525-.116 2.416-.836 4.288-2.161 5.613s-3.195 2.045-5.613 2.161c-1.023.057-2.864.087-5.525.087s-4.502-.029-5.525-.087c-2.416-.116-4.287-.836-5.612-2.161s-2.045-3.195-2.161-5.613c-.059-1.021-.087-2.864-.087-5.525s.029-4.502.087-5.525c.116-2.416.836-4.287 2.161-5.612s3.195-2.045 5.612-2.161c1.021-.057 2.864-.087 5.525-.087s4.502.029 5.525.087c2.416.116 4.288.836 5.613 2.161s2.045 3.195 2.161 5.612c.059 1.023.087 2.864.087 5.525zM17.396 4.948c-.807.005-1.252.009-1.334.009s-.525-.004-1.334-.009c-.807-.005-1.42-.005-1.839 0-.418.005-.979.023-1.682.052s-1.302.088-1.795.175c-.495.088-.909.195-1.246.323-.58.232-1.093.57-1.534 1.011s-.779.954-1.011 1.534c-.129.338-.236.752-.323 1.246s-.145 1.093-.175 1.795c-.029.704-.046 1.264-.052 1.682s-.005 1.032 0 1.839c.005.807.009 1.252.009 1.334s-.004.525-.009 1.334c-.005.807-.005 1.42 0 1.839.005.418.023.979.052 1.682s.088 1.302.175 1.795c.088.495.195.909.323 1.246.232.58.57 1.093 1.011 1.534s.952.779 1.534 1.011c.338.129.752.236 1.246.323.493.087 1.093.145 1.795.175.704.029 1.264.046 1.682.052s1.03.005 1.839 0c.807-.005 1.252-.009 1.334-.009.08 0 .525.004 1.334.009.807.005 1.42.005 1.839 0 .418-.005.979-.023 1.682-.052s1.302-.087 1.795-.175c.493-.087.909-.195 1.246-.323.58-.232 1.093-.57 1.534-1.011s.779-.952 1.011-1.534c.129-.337.236-.752.323-1.246.087-.493.145-1.093.175-1.795.029-.704.046-1.264.052-1.682s.005-1.03 0-1.839c-.005-.807-.009-1.252-.009-1.334 0-.08.004-.525.009-1.334.005-.807.005-1.42 0-1.839-.005-.418-.023-.979-.052-1.682s-.087-1.302-.175-1.795c-.087-.493-.195-.909-.323-1.246-.232-.58-.57-1.093-1.011-1.534s-.954-.779-1.534-1.011c-.337-.129-.752-.236-1.246-.323S21.619 5.03 20.917 5c-.704-.029-1.264-.046-1.682-.052-.418-.007-1.03-.007-1.839 0zm3.531 6.125c1.336 1.336 2.004 2.957 2.004 4.862s-.668 3.527-2.004 4.863c-1.336 1.336-2.957 2.004-4.863 2.004s-3.527-.668-4.863-2.004c-1.338-1.336-2.005-2.957-2.005-4.863s.668-3.527 2.004-4.863c1.336-1.336 2.957-2.004 4.863-2.004 1.907 0 3.527.668 4.864 2.004zm-1.709 8.018c.871-.871 1.307-1.923 1.307-3.155s-.436-2.284-1.307-3.155-1.923-1.307-3.155-1.307-2.284.436-3.155 1.307-1.307 1.923-1.307 3.155.436 2.284 1.307 3.155 1.923 1.307 3.155 1.307 2.284-.436 3.155-1.307zm5.125-11.434c.314.314.471.691.471 1.132s-.157.82-.471 1.132c-.314.314-.691.471-1.132.471s-.82-.157-1.132-.471c-.314-.314-.471-.691-.471-1.132s.157-.82.471-1.132c.314-.314.691-.471 1.132-.471.441.002.818.159 1.132.471z"/></svg>';

  run_wp_cli_command("menu item add-custom $secondary_menu_id '$facebook_svg' 'https://www.facebook.com/Create-Raw-Vision-596361277187093/'");
  run_wp_cli_command("menu item add-custom $secondary_menu_id '$pinterest_svg' 'https://de.pinterest.com/CreateRawVision'");
  run_wp_cli_command("menu item add-custom $secondary_menu_id '$youtube_svg' 'https://www.youtube.com/channel/UCDn-CVZvNd6xqXM0g1Zu4pg'");
  run_wp_cli_command("menu item add-custom $secondary_menu_id '$twitter_svg' 'https://twitter.com/CreateRawVision'");
  run_wp_cli_command("menu item add-custom $secondary_menu_id '$instagram_svg' 'https://www.instagram.com/createrawvision/'");

  // Assign locations
  run_wp_cli_command('menu location remove hauptmenu primary');
  run_wp_cli_command("menu location assign $main_menu_id primary");
  run_wp_cli_command("menu location assign $secondary_menu_id secondary");


  /**
   * Activate breadcrumbs
   */
  genesis_update_settings([
    'breadcrumb_single' => 1,
    'breadcrumb_page' => 0,
    'breadcrumb_404' => 0,
    'breadcrumb_attachment' => 0,
    'breadcrumb_home' => 0,
    'breadcrumb_front_page' => 0,
    'breadcrumb_posts_page' => 0,
    'breadcrumb_archive' => 1
  ]);


  
  /**
   * Set the first image of a post as teaser image
   */

  WP_CLI::log('Setting teaser image for all member posts');

  $teaser_image_field = array_keys(array_filter(acf_get_local_fields(), function ($value, $key) {
    return $value['name'] == 'teaser_image';
  }, ARRAY_FILTER_USE_BOTH))[0];

  if (is_null($teaser_image_field)) {
    WP_CLI::error('Couldnt find teaser_image field. Aborting deployment.');
  }

  $member_posts = get_posts([
    'numberposts' => -1,
    'category_name' => 'member',
    'post_status' => 'any'
  ]);

  foreach ($member_posts as $post) {
    preg_match('/<img.+?class=[\'"].*?wp-image-(\d*).*?[\'"].*?>/i', $post->post_content, $matches);
    if (count($matches) == 0) {
      WP_CLI::warning("Couldn't find first image in post $post->post_title");
      continue;
    }
    $first_image_id = $matches[1];
    $success = acf_save_post($post->ID, [$teaser_image_field => $first_image_id]);
  }



  // Update verion in database
  update_option($version_option_name, $new_version);
  WP_CLI::success("Deployed version " . $new_version);
}

WP_CLI::log("Flushing all caches");
run_wp_cli_command('sg purge');
run_wp_cli_command("autoptimize clear");
run_wp_cli_command("cache flush");
run_wp_cli_command("rewrite flush");

WP_CLI::success('Deployment complete');
