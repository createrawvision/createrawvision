<div class="crv-shop">
<?php
// All shop terms.
$shop_terms = get_terms( array( 'taxonomy' => 'crv_shop_category' ) );

foreach ( $shop_terms as $shop_term ) :

	echo '<h2 class="crv-shop__term-name">' . esc_html( $shop_term->name ) . '</h2>';
	echo '<div class="crv-shop__item-list">';

	// All shop items for the term.
	$query = new WP_Query(
		array(
			'nopaging'  => true,
			'post_type' => 'crv_shop_item',
			'tax_query' => array(
				array(
					'taxonomy' => 'crv_shop_category',
					'fields'   => 'term_id',
					'terms'    => $shop_term->term_id,
				),
			),
		)
	);

	while ( $query->have_posts() ) :
		$query->the_post();
		?>
		<div class="crv-shop__item">
			<?php the_post_thumbnail( array( 300, 300 ), array( 'class' => 'crv-shop__item-image' ) ); ?>
			<div class="crv-shop__item-content">
				<h3 class="crv-shop__item-title"><?php the_title(); ?></h3>
				<div class="crv-shop__item-description"><?php the_content(); ?></div>
			</div>
			<a href="<?php the_field( 'product_url' ); ?>">
				<button class="crv-shop__item-button"><?php the_field( 'button_text' ); ?></button>
			</a>
		</div>
		<?php
	endwhile;

	wp_reset_postdata();

	echo '</div>';

endforeach;
?>
</div>
