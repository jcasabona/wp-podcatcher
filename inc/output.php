<?php
/**
 *  Functions to control the output of sponsors & more.
 *
 * @package wp_podcatcher
 */

 // Deprecated
function wpp_fusebox_insert( $content ) {
	return false;
}

/**
 * Callback function to insert sponsors into content on episode pages.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_sponsors( $content ) {
	$sponsor_output = ( is_single() ) ? wpp_get_sponsors() : wpp_get_sponsors_feed();

	if ( ! $sponsor_output ) { 
		return $content;
	}

	if( ! is_feed() ) {
		return $content . $sponsor_output;
	} else {
		return $content . $sponsor_output;
	}
}

/**
 * Callback function to insert sponsors into content on SSP feed
 *
 * @param String $content from WordPress editor.
 */
function wpp_spp_append_sponsors( $content ) {
	$sponsor_output = wpp_get_sponsors_feed();

	if ( ! $sponsor_output ) { 
		return $content;
	}

	return $content . $sponsor_output;
}

// Filter uses above function.
//add_filter( 'the_content', 'wpp_append_sponsors', 10 );
add_filter( 'ssp_feed_item_content', 'wpp_spp_append_sponsors', 10 );


function wpp_remove_empty_p( $content ) {
	$content = force_balance_tags( $content );
	$content = preg_replace( '~\s?<p>(&nbsp;)+</p>\s?~', '', $content );
	return $content;
}
add_filter('the_content', 'wpp_remove_empty_p', 99999);