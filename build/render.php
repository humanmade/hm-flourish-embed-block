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

// Build thumbnail URL.
$thumbnail_url = 'https://public.flourish.studio/' . esc_attr( $data_src ) . '/thumbnail';

// Exit early if nothing to render.
if ( empty( $data_src ) ) {
	return;
}

// Get wrapper attributes for WordPress block styles and classes.
$wrapper_attributes = get_block_wrapper_attributes();

// Check if this is a sandboxed preview.
$is_sandboxed = isset( $_GET['sandboxedPreview'] ) && $_GET['sandboxedPreview'] === '1';

// Determine whether to show fallback.
$show_fallback = ! $is_sandboxed && is_feed();

// For sandboxed previews and RSS feeds, output the script inline to ensure it loads correctly.
$output_script_inline = $is_sandboxed || is_feed();

echo '<div ' . wp_kses_data( $wrapper_attributes ) . '>';

if ( $show_fallback ) {
	// Show thumbnail fallback in RSS feeds.
	echo '<img src="' . esc_attr( $thumbnail_url ) . '" alt="Flourish visualization" style="max-width: 100%; height: auto;" />';
} else {
	// Show full embed with noscript fallback.
	echo '<div class="flourish-embed flourish-chart" data-src="' . esc_attr( $data_src ) . '"></div>';
	echo '<noscript><img src="' . esc_attr( $thumbnail_url ) . '" alt="Flourish visualization" style="max-width: 100%; height: auto;" /></noscript>';

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
