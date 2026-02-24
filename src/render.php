<?php
/**
 * Render callback for the flourish-embed block.
 *
 * @package hm-flourish-block
 */

// Get block attributes.
$type                    = isset( $attributes['type'] ) ? $attributes['type'] : 'visualisation';
$id                      = isset( $attributes['id'] ) ? $attributes['id'] : '';
$fallback_image_id       = isset( $attributes['fallbackImageId'] ) ? absint( $attributes['fallbackImageId'] ) : 0;
$use_fallback_for_rss    = isset( $attributes['useFallbackImageForRSS'] ) ? (bool) $attributes['useFallbackImageForRSS'] : false;
$fallback_image_html     = $fallback_image_id ? wp_get_attachment_image(
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
$show_fallback = ! $is_sandboxed && ( empty( $data_src ) || ( is_feed() && $use_fallback_for_rss ) ) && $fallback_image_html;

// For sandboxed previews and RSS feeds with fallback enabled, we need to output the script inline to ensure it loads correctly.
$output_script_inline = $is_sandboxed || is_feed();

echo '<div ' . wp_kses_data( $wrapper_attributes ) . '>';

if ( $show_fallback && $fallback_image_html ) {
	echo wp_kses_post( $fallback_image_html );
} elseif ( ! empty( $data_src ) ) {
	echo '<div class="flourish-embed flourish-chart" data-src="' . esc_attr( $data_src ) . '"></div>';

	// For sandboxed previews, add cache-busting to ensure script executes in each iframe.
	if ( $output_script_inline ) {
		if ( $is_sandboxed ) {
			// Get the registered script and modify its version for cache-busting.
			$wp_scripts = wp_scripts();
			if ( isset( $wp_scripts->registered['flourish-embed'] ) ) {
				$wp_scripts->registered['flourish-embed']->ver = uniqid();
			}
		}

		wp_print_scripts( 'flourish-embed' );
	}
}

echo '</div>';
