<article class="entry">
	<header class="entry-header">
		<h2 class="entry-title">
			<a class="entry-title-link" href="<?php echo esc_url( $link ); ?>">
				<?php echo wp_kses_post( $title ); ?>
			</a>
		</h2>
	</header>
	<?php if ( $image_id ) : ?>
		<div class="entry-content">
			<a class="entry-image-link" href="<?php echo esc_url( $link ); ?>">
				<?php
				echo wp_get_attachment_image(
					$image_id,
					$image_size ?? 'thumbnail-portrait',
					false,
					array( 'class' => 'alignleft' )
				);
				?>
			</a>
		</div>
	<?php endif; ?>
</article>
