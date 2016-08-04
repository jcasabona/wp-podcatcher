<?php
/**
 * Plugin Name: WP Podcatcher
 * Plugin URI: http://howibuilt.it/
 * Description: This plugin allows you to properly power your Podcasting site
 * Author: Joe Casabona
 * Version: 1.0
 * Author URI: http://casabona.org/

 * @package wp-podcatcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Include the Goods */
include_once( 'fm/fieldmanager.php' );
include_once( 'inc/functions.php' );

/* Tell user if Field Manager faild to load */
if ( ! defined( 'FM_VERSION' ) ) {
	add_action( 'admin_notices', 'wppc_no_fm' );
}