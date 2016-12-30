<?php
/**
 *  Functions to control the output of episodes and sponsors.
 *
 * @package wp_podcatcher
 */

add_filter( 'the_content', 'wpp_append_embeded_episode' );

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
