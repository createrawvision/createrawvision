<div class="wpb-single wpb-item collection_<?php echo intval( $id );?> active">
	<p class="wpb-thumb">
		<a href="<?php echo esc_url( get_permalink( $bkid ) );?>"> <?php echo wp_kses( $wpb->post_thumb( $bkid, 100 ), $wpb->wpb_allowed_html() );?>
		</a>
	</p>
	<p class="wpb-title">
		<a href="<?php echo esc_url( get_permalink( $bkid ) );?>"> <?php echo get_the_title( $bkid );?>
		</a>
	</p>
	<p class="wpb-action-remove">
		<i class="wpb-icon-trash" data-post_id="<?php echo intval( $bkid );?>"
			data-collection_id="<?php echo intval( $id );?>"></i>
	</p>
</div>
