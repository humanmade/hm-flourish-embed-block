<?php
/**
 * Render callback for the flourish-embed block.
 *
 * @package hm-flourish-block
 */

// Get block attributes.
$type                = isset( $attributes['type'] ) ? $attributes['type'] : 'visualisation';
$id                  = isset( $attributes['id'] ) ? $attributes['id'] : '';
$fallback_image_id   = isset( $attributes['fallbackImageId'] ) ? absint( $attributes['fallbackImageId'] ) : 0;
$fallback_image_html = $fallback_image_id ? wp_get_attachment_image(
	$fallback_image_id,
	'full',
	false,
	[
		'style' => 'max-width: 100%; height: auto;',
	]
) : '';

// Build the data-src value.
$data_src = $id ? $type . '/' . esc_attr( $id ) : '';

// Exit early if nothing to render.
if ( empty( $data_src ) && empty( $fallback_image_html ) ) {
	return;
}

// Get wrapper attributes for WordPress block styles and classes.
$wrapper_attributes = get_block_wrapper_attributes();

// Check if this is a sandboxed preview.
$is_sandboxed = isset( $_GET['sandboxedPreview'] ) && $_GET['sandboxedPreview'] === '1';

// Determine whether to show fallback or embed.
$show_fallback = ( empty( $data_src ) || is_feed() ) && $fallback_image_html;

echo '<div ' . wp_kses_data( $wrapper_attributes ) . '>';

if ( $show_fallback && $fallback_image_html ) {
	echo wp_kses_post( $fallback_image_html );
} elseif ( ! empty( $data_src ) ) {
	echo '<div class="flourish-embed flourish-chart" data-src="' . esc_attr( $data_src ) . '"></div>';

	// For sandboxed previews, output the script inline.
	if ( $is_sandboxed ) {
		wp_print_scripts( 'flourish-embed' );
	}
}

echo '</div>';
