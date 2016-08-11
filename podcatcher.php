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

// Create ad image sizes.
add_image_size( 'wpp-full-banner', 468, 60 );
add_image_size( 'wpp-leaderboard', 728, 90 );
add_image_size( 'wpp-big-square', 336, 280 );
add_image_size( 'wpp-small-square', 300, 250 );
