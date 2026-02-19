<?php
/**
 * Render callback for the flourish-embed block.
 *
 * @package hm-flourish-block
 */

// Get block attributes.
$type = isset( $attributes['type'] ) ? $attributes['type'] : 'visualisation';
$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';

// Build the data-src value.
$data_src = $id ? $type . '/' . esc_attr( $id ) : '';

if ( empty( $data_src ) ) {
	return;
}

// Get wrapper attributes for WordPress block styles and classes.
$wrapper_attributes = get_block_wrapper_attributes();

echo '<div ' . wp_kses_data( $wrapper_attributes ) . '>';
echo '<div class="flourish-embed flourish-chart" data-src="' . esc_attr( $data_src ) . '"></div>';
echo '</div>';
