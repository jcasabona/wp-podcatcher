<?php
/**
 *  Functions to control the output of episodes and sponsors.
 *
 * @package wp_podcatcher
 */

/**
 * Callback function to insert audio embed into content on episode pages.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_embeded_episode( $content ) {

	if ( is_singular( 'episode' ) ) {
		$audio_file = wpp_get_audio_file();
		if ( $audio_file ) {
			$shortcode = sprintf( '[audio src="%s"]', esc_url( $audio_file ) );
			$content = $content . do_shortcode( $shortcode );
		}
	}

	return $content;
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_embeded_episode' );


/**
 * Callback function to insert sponsors into content on episode pages.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_sponsors( $content ) {
	return $content . wpp_get_sponsors();
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_sponsors' );
