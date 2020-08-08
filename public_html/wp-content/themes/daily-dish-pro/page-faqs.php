<?php

require_once CHILD_DIR . '/lib/faqs/faq-functions.php';

// Remove the default Genesis loop.
remove_action( 'genesis_loop', 'genesis_do_loop' );
// Replace it with a custom one.
add_action( 'genesis_loop', 'crv_faqs_loop' );

// Run the Genesis loop.
genesis();
