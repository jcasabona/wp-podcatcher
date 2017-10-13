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
	$sponsor_output = wpp_get_sponsors();

	if ( ! $sponsor_output ) {
		return $content;
	}

	return $content . $sponsor_output;
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_sponsors' );


/**
 *  Functions to control the output of transcripts.
 *
 * @package wp_podcatcher
 */

/**
 * Callback function to insert transcripts into content on episode pages.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_transcript( $content ) {
	$transcript_output = wpp_get_transcript();

	if ( ! $transcript_output ) {
		return $content;
	}

	return $content . $transcript_output;
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_transcript' );
