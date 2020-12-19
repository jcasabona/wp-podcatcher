<?php
/**
 *  Functions to control the output of sponsors & more.
 *
 * @package wp_podcatcher
 */

 /**
 * Callback function to insert Fusebox player into content.
 *
 * @param String $content from WordPress editor.
 */
function wpp_fusebox_insert( $content ) {

	// No player in the Feed!
	if (is_feed() ) {
		return $content;
	}

	$url = wpp_get_media_URL();

	if ( $url ) {
		$format = '[fusebox_track_player url="%s" title="%s"]';
		return  do_shortcode( sprintf( $format, $url, get_the_title() ) ). $content;
	}

	return $content;
}

//add_filter( 'the_content', 'wpp_fusebox_insert', 100 );
//add_filter( 'the_excerpt', 'wpp_fusebox_insert', 100 );
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
		return $sponsor_output . $content;
	} else {
		return $content . $sponsor_output;
	}
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_sponsors', 10 );


/**
 * Callback function to insert sponsors into feed on episodes feed.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_sponsors_feed( $content ) {
	$sponsor_output = wpp_get_sponsors_feed();

	if ( ! $sponsor_output || ! is_feed() ) {  
		return $content;
	}

	return $content . apply_filters( 'the_content', $sponsor_output );
}
// add_filter( 'the_excerpt_rss', 'wpp_append_sponsors_feed', 7 );
// add_filter( 'the_content_rss', 'wpp_append_sponsors_feed', 7 );

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
add_filter( 'the_content', 'wpp_append_transcript', 9 );

function wpp_remove_empty_p( $content ) {
	$content = force_balance_tags( $content );
	$content = preg_replace( '~\s?<p>(&nbsp;)+</p>\s?~', '', $content );
	return $content;
}
add_filter('the_content', 'wpp_remove_empty_p', 99999);