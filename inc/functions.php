<?php
/**
 * A general functions file
 *
 * @package wp-podcatcher
 */

function wppc_no_fm() {
?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php esc_html_e( 'Fieldmanager should be here. Something has gone wrong. Contact joe@wpinonemonth.com', 'wp-podcatcher' ); ?>
			</p>
		</div>
<?php
}

// Start Your Engines.
require_once( 'post-types/parent-class.php' );


function wpp_get_audio_file() {
	$audio_group = get_post_meta( get_the_id(), 'wpp_audio_group', true );

	if ( ! empty( $audio_group['wpp_audio_file'] ) ) {
		return wp_get_attachment_url( $audio_group['wpp_audio_file'] );
	} else if ( ! empty( $audio_group['wpp_audio_link'] ) ) {
		return $audio_group['wpp_audio_link'];
	}

	return false;
}
