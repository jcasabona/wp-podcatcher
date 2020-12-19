<?php
/**
 *  WPP_Transctipts registers the Transcript post type
 *
 * @package wp_podcatcher
 */

/**
 * WPP_Transcripts registers the episode post type
 *
 * @package wp_podcatcher
 **/
class WPP_Transcripts extends WP_Podcatcher {

	/**
	 * Our construct
	 */
	public function __construct() {
		$this->name = 'transcript';
		$this->plural_name = 'transcripts';
		$this->icon .= 'media-text';
		$this->gutes = true;
		parent::__construct();
	}
} // END class

new WPP_Transcripts();