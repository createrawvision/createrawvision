<?php
if ( ! isset( $array['label'] ) ) {
	$array = array();
	$array['label'] = $$key;
}
if ( 0 === $id ) {
	$class = 'active visited';
} else {
	$class = '';
}
?>
<button
	class="wpb-button button <?php echo esc_attr( $class );?> collection_<?php echo intval( $id );?>"
	id="<?php echo intval( $id );?>">
	<a href="#collection_<?php echo intval( $id );?>"
		data-collection_id="<?php echo intval( $id );?>"><?php echo esc_attr( $array['label'] );?> </a>
</button>
