<?php
/**
 * Plugin Name: WP Podcatcher
 * Plugin URI: http://howibuilt.it/
 * Description: This plugin adds some extra features to Castos to take your podcasting site to the next level.
 * Author: Joe Casabona
 * Version: 2.0
 * Author URI: http://casabona.org/

 * @package wp-podcatcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Tell user if Seriously Simple Podcasting is not installed, then kill it. NOT IN USE 2022-02-05 */
function wpp_check_for_castos() {
	if ( ! defined( 'SSP_CASTOS_APP_URL') ) {
		add_action( 'admin_notices', function() {
			?>
					<div class="notice notice-warning is-dismissible">
						<p>
							<?php esc_html_e( 'It looks like Seriously Simple Podcasting is not installed. We strongly recommend you use that with this plugin.', 'wp-podcatcher' ); ?>
						</p>
					</div>
			<?php
		} );
	}
}

// add_action( 'plugins_loaded', 'wpp_check_for_castos' );

define( 'WPP_VERSION', '1.0' );
define( 'WPP_URL', plugin_dir_url( __FILE__ ) );
define( 'WPP_ASSETS', WPP_URL . '/assets/' );

/* Include the Goods */
include_once( 'inc/functions.php' );
include_once( 'inc/output.php' );
// include_once( 'inc/widgets/widgets.php' );
// include_once( 'inc/patterns.php' );

// Create ad image sizes.
add_image_size( 'wpp-full-banner', 468, 60 );
add_image_size( 'wpp-leaderboard', 728, 90 );
add_image_size( 'wpp-big-square', 336, 280 );
add_image_size( 'wpp-small-square', 300, 250 );

/**
 * Function to enqueue any scripts and styles. NOT IN USE 2022-02-05
 */
function wpp_enqueue_assets() {
	wp_enqueue_style( 'wpp_style', WPP_ASSETS . 'style.css' );
}

//add_action( 'wp_enqueue_scripts', 'wpp_enqueue_assets' );

add_filter( 'ssp_episode_download_link', 'ssp_use_raw_audio_file_url', 10, 3 );
function ssp_use_raw_audio_file_url ( $url, $episode_id, $file ) {
  return $file;
}

// ACF Display Custom Fields
add_filter('acf/settings/remove_wp_meta_box', '__return_false');
