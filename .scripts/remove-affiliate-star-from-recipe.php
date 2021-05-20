<?php
if ( ! defined( 'WP_CLI' ) ) {
	echo 'WP_CLI not defined';
	exit( 1 );
}

$recipe_ids = get_posts(
	array(
		'post_type'   => 'wprm_recipe',
		'post_status' => 'any',
		'nopaging'    => true,
		'fields'      => 'ids',
	)
);

$progressbar = \WP_CLI\Utils\make_progress_bar( 'Removing leading stars from recipe ingredients', count( $recipe_ids ) );

foreach ( $recipe_ids as $recipe_id ) {
	$ingredient_groups = get_post_meta( $recipe_id, 'wprm_ingredients', true );

	foreach ( $ingredient_groups as &$ingredient_group ) {
		$ingredients = &$ingredient_group['ingredients'];

		foreach ( $ingredients as &$ingredient ) {
			$ingredient['notes'] = strip_leading_star( $ingredient['notes'] );
		}
		unset($ingredient, $ingredients);
	}
	unset($ingredient_group);

	update_post_meta( $recipe_id, 'wprm_ingredients', $ingredient_groups );

	$progressbar->tick();
}
$progressbar->finish();

function strip_leading_star( $str ) {
	return preg_replace( '/^\s*\*\s*/', '', $str, 1 );
}
