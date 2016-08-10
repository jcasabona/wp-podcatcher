<?php
/**
 *  WPP_Sponsors registers the episode post type
 *
 * @package wp_podcatcher
 */

/**
 * WPP_Sponsors registers the episode post type
 *
 * @package wp_podcatcher
 **/
class WPP_Sponsors extends WP_Podcatcher {

	/**
	 * Our construct
	 */
	public function __construct() {
		$this->name = 'sponsor';
		$this->plural_name = 'sponsors';
		$this->icon .= 'id';
		parent::__construct();
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {
		// Slug for the time being.
		return;
	}
} // END class

new WPP_Sponsors();
