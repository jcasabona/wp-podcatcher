<?php
/**
 *  Functions to control the output of sponsors & more.
 *
 * @package wp_podcatcher
 */

/**
 * Callback function to insert sponsors into content on episode pages.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_sponsors( $content ) {
	$sponsor_section = '<h4>Sponsored by:</h4>' . wpp_get_sponsors();

	return $content . $sponsor_section;
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_sponsors' );
