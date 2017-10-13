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
		parent::__construct();
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {
		$fm = new Fieldmanager_Media( array(
			'name' => 'wpp_transcript_file',
		) );
		$fm->add_meta_box( 'Transcript File', array( $this->name ) );
	}
} // END class

new WPP_Transcripts();