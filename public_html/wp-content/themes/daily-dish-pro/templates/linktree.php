<?php
/**
 * Template for displaying a linktree for usage in social media.
 */

global $rcp_options;
$registration_page_url_raw = add_query_arg( 'level', 2, get_permalink( $rcp_options['registration_page'] ) );
$recipes_category_id       = 5869;
$recipes_overview_url_raw  = get_category_link( $recipes_category_id );
?>

<div class="linktree">
	<header>
		<?php echo wp_get_attachment_image( 22786, 'thumbnail', false, array( 'class' => 'linktree__profile-image' ) ); ?>
		<?php echo wp_get_attachment_image( 20555, 'medium', false, array( 'class' => 'linktree__logo' ) ); ?>
	</header>
	<a href="<?php echo esc_url( home_url() ); ?>" target="_blank"><button>Mein Blog</button></a>
	<a href="https://www.digistore24.com/redir/341726/createrawvision" target="_blank"><button>Kostenlose Anmeldung<br>„Rohkost Leicht Gemacht“<br>Herbst-Online Festival</button></a>
	<a href="<?php echo esc_url( $registration_page_url_raw ); ?>"><button>Für Mitgliedschaft Registrieren</button></a>
	<a href="<?php echo esc_url( $recipes_overview_url_raw ); ?>" target="_blank"><button>Rezeptübersicht</button></a>
	<?php
	// Show all published linktree cards.
	$query = new WP_Query(
		array(
			'post_type' => 'crv_linktree_card',
			'nopaging'  => true,
			'orderby'   => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
		)
	);
	if ( $query->have_posts() ) :
		echo '<div class="linktree__cards">';
		while ( $query->have_posts() ) :
			$query->the_post();

			echo '<div class="linktree__card">';
			echo '<a href="' . esc_url( get_field( 'linktree_link' ) ) . '" target="_blank">';
			echo get_the_post_thumbnail( get_the_ID(), array( 300, 300 ) );
			echo '<p class="linktree__card__title">' . esc_html( get_the_title() ) . '</p>';
			echo '</a></div>';
		endwhile;
		echo '</div>';
	endif;
	wp_reset_postdata();
	?>
	<a href="<?php the_permalink( get_page_by_path( 'geschenk' ) ); ?>" target="_blank"><button>Newsletter + Geschenk</button></a>
	<p class="linktree__title">@createrawvision</p>
	<?php echo do_shortcode( '[widget id="jetpack_widget_social_icons-2"]' ); ?>
</div>
