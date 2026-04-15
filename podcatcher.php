<?php
/**
 * Plugin Name: WP Podcatcher
 * Plugin URI: http://howibuilt.it/
 * Description: This plugin adds some extra features to Castos to take your podcasting site to the next level.
 * Author: Joe Casabona
 * Version: 3.0
 * Author URI: http://casabona.org/

 * @package wp-podcatcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPP_VERSION', '3.0' );
define( 'WPP_URL', plugin_dir_url( __FILE__ ) );
define( 'WPP_ASSETS', WPP_URL . '/assets/' );

/* Include the Goods */
include_once( 'inc/functions.php' );
include_once( 'inc/output.php' );

// Create ad image sizes.
add_image_size( 'wpp-full-banner', 468, 60 );
add_image_size( 'wpp-leaderboard', 728, 90 );
add_image_size( 'wpp-big-square', 336, 280 );
add_image_size( 'wpp-small-square', 300, 250 );


// ACF Display Custom Fields
add_filter('acf/settings/remove_wp_meta_box', '__return_false');

// 1. Register the admin page
function streamlined_add_admin_page() {
	add_submenu_page(
		'tools.php',
		'Replace Podcast Blocks',
		'Replace Podcast Blocks',
		'manage_options',
		'replace-podcast-blocks',
		'streamlined_render_admin_page'
	);
}
//add_action('admin_menu', 'streamlined_add_admin_page');

// 2. Render the admin page
function streamlined_render_admin_page() {
	if ( ! current_user_can('manage_options') ) {
		wp_die('Unauthorized');
	}

	$logs = [];

	// Handle the form submission
	if ( isset($_POST['run_replacement']) && check_admin_referer('streamlined_run_replacement') ) {
		$logs = streamlined_replace_top_kadence_template_with_transistor_embed();
	}

	?>
	<div class="wrap">
		<h1>Replace Podcast Blocks</h1>
		<p>This will find the top 3 Kadence rowlayout blocks on each post, extract the Transistor embed, and replace the blocks with just the embed.</p>

		<form method="post">
			<?php wp_nonce_field('streamlined_run_replacement'); ?>
			<p><input type="submit" name="run_replacement" class="button button-primary" value="Replace Top 3 Kadence Blocks with Transistor Embed"></p>
		</form>

		<?php if ( ! empty($logs) ) : ?>
			<h2>Result Log:</h2>
			<ul>
				<?php foreach ( $logs as $log ) : ?>
					<li><?php echo esc_html($log); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
}

// 3. The actual replacement logic (returns a log array instead of using error_log)
function streamlined_replace_top_kadence_template_with_transistor_embed() {
	$posts = get_posts([
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'numberposts'    => -1,
		'suppress_filters' => false,
	]);

	$log = [];

	foreach ( $posts as $post ) {
		$content = $post->post_content;

		// Match everything from the first kadence rowlayout to the third closing tag
		if ( preg_match(
			'/^(.*?<!-- \/wp:kadence\/rowlayout -->.*?<!-- \/wp:kadence\/rowlayout -->.*?<!-- \/wp:kadence\/rowlayout -->)/is',
			$content,
			$block_match
		)) {
			$full_block = $block_match[1];

			// Extract iframe
			if ( preg_match('/<iframe[^>]+src="https:\/\/share\.transistor\.fm\/e\/[^"]+"[^>]*><\/iframe>/i', $full_block, $iframe_match) ) {
				$iframe = $iframe_match[0];
				$replacement = '<div class="transistor-embed">' . $iframe . '</div>';

				// Replace the full matched block with iframe
				$new_content = str_replace($full_block, $replacement, $content);

				if ( $new_content !== $content ) {
					wp_update_post([
						'ID' => $post->ID,
						'post_content' => $new_content,
					]);
					$log[] = "✅ Updated post ID {$post->ID}";
				} else {
					$log[] = "⚠️ Post ID {$post->ID} — match found but replacement failed";
				}
			} else {
				$log[] = "❌ Iframe not found inside top blocks for post ID {$post->ID}";
			}
		} else {
			$log[] = "ℹ️ Post ID {$post->ID} — top 3 kadence blocks not found";
		}
	}

	return $log;
}