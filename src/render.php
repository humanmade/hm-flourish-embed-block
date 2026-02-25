<?php
/**
 * Render callback for the flourish-embed block.
 *
 * @package hm-flourish-block
 */

// Get block attributes.
$type = isset( $attributes['type'] ) ? $attributes['type'] : 'visualisation';
$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';

// Exit early if nothing to render.
if ( empty( $id ) ) {
	return;
}

// Build thumbnail URL.
$thumbnail_url = sprintf( 'https://public.flourish.studio/%s/%s/thumbnail', $type, $id );

// Check if this is a sandboxed preview.
$is_sandboxed = isset( $_GET['sandboxedPreview'] ) && $_GET['sandboxedPreview'] === '1';

// Determine whether to show fallback.
$show_fallback = is_feed();

// For sandboxed previews and RSS feeds, output the script inline to ensure it loads correctly.
$output_script_inline = $is_sandboxed || is_feed();

printf( '<div %s>', wp_kses_data( get_block_wrapper_attributes() ) );

if ( $show_fallback ) {
	// Show thumbnail fallback in RSS feeds.
	printf(
		'<img src="%s" alt="Flourish visualization" style="max-width: 100%%; height: auto;" />',
		esc_url( $thumbnail_url )
	);
} else {
	// Show full embed with noscript fallback.
	printf(
		'<div class="flourish-embed flourish-chart" data-src="%s/%s"></div>',
		esc_attr( $type ),
		esc_attr( $id )
	);
	printf(
		'<noscript><img src="%s" alt="Flourish visualization" style="max-width: 100%%; height: auto;" /></noscript>',
		esc_url( $thumbnail_url )
	);

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
