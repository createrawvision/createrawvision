<div class="linktree">
	<header>
		<?php echo wp_get_attachment_image( 22786, 'thumbnail', false, array( 'class' => 'linktree__profile-image' ) ); ?>
		<?php echo wp_get_attachment_image( 20555, 'medium', false, array( 'class' => 'linktree__logo' ) ); ?>
	</header>
	<button>Mein Blog</button>
	<button>Für Mitgliedschaft Registrieren</button>
	<button>Rezeptübersicht</button>
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
			echo '<a href="' . esc_url( get_field( 'linktree_link' ) ) . '">';
			echo get_the_post_thumbnail( get_the_ID(), array( 300, 300 ) );
			echo '<p class="linktree__card__title">' . esc_html( get_the_title() ) . '</p>';
			echo '</a></div>';
		endwhile;
		echo '</div>';
	endif;
	wp_reset_postdata();

	echo do_shortcode( '[popup_trigger id="22782" tag="button"]Newsletter Anmeldung[/popup_trigger]' );
	echo '<p class="linktree__title">@createrawvision</p>';
	echo do_shortcode( '[widget id="jetpack_widget_social_icons-2"]' );
	?>
</div>
