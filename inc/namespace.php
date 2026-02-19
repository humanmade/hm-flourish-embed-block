<?php
/**
 * Plugin setup and block registration.
 */

namespace HM\Flourish;

/**
 * Add hooks.
 */
function bootstrap(): void {
	add_action( 'init', __NAMESPACE__ . '\\register_blocks' );
}

/**
 * Register the flourish-embed block.
 */
function register_blocks(): void {
	// Register the Flourish embed script.
	wp_register_script(
		'flourish-embed',
		'https://public.flourish.studio/resources/embed.js',
		[],
		null,
		true
	);

	register_block_type(
		PLUGIN_DIR_PATH . '/build',
		[
			'view_script' => 'flourish-embed',
		]
	);
}
