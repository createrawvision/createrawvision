<?php

require_once CHILD_DIR . '/lib/faqs/faq-functions.php';

// Append the FAQ content.
add_action( 'genesis_after_entry_content', 'crv_faqs_loop' );

// Run the Genesis loop.
genesis();
