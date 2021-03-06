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
		$this->gutes = true;
		parent::__construct();
	}
} // END class

new WPP_Sponsors();