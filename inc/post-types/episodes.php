<?php
/**
 *  WPP_Episodes registers the episode post type
 *
 * @package wp_podcatcher
 */

/**
 * WPP_Episodes registers the episode post type
 *
 * @package wp_podcatcher
 **/
class WPP_Episodes extends WP_Podcatcher {

	/**
	 * Our construct
	 */
	public function __construct() {
		 parent::__construct( 'episode', 'episodes', 'microphone' );
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {
		// Slug for the time being.
		return;
	}
} // END class

new WPP_Episodes();
