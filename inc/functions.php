<?php
/**
 * A general functions file
 *
 * @package wp-podcatcher
 */

/**
 * Display a notification if Fieldmanager can't be found.
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

/**
 * Get the audio link that should be embedded. Prioritize uploaded file over link.\
 *
 * @return link if found, false if not.
 */
function wpp_get_audio_file() {
	$audio_group = get_post_meta( get_the_id(), 'wpp_audio_group', true );

	if ( ! empty( $audio_group['wpp_audio_file'] ) ) {
		return wp_get_attachment_url( $audio_group['wpp_audio_file'] );
	} else if ( ! empty( $audio_group['wpp_audio_link'] ) ) {
		return $audio_group['wpp_audio_link'];
	}

	return false;
}

/**
 * Function to get latest WPP Episode.
 *
 * @return Array
 */
function wpp_get_latest_episode() {
	$episode = new WP_Query( array( 'numberposts' => 1, 'post_type' => 'episode' ) );
	$episode_data = array();

	while ( $episode->have_posts() ) {
		$episode->the_post();
		$episode_data['ID'] = get_the_id();
		$episode_data['title'] = get_the_title();
		$episode_data['audio_file'] = wpp_get_audio_file();
		$episode_data['permalink'] = get_permalink();
		$episode_data['thumbnail_id'] = get_post_thumbnail_id( get_the_id() );
	}
	wp_reset_postdata();

	return $episode_data;
}

/**
 * Generate HTML for displaying sponsors associated with episode.
 *
 * @return HTML string if there are sponsors, false if there are not.
 */
function wpp_get_sponsors() {
	$sponsor_ids = get_post_meta( get_the_id(), 'wpp_episode_sponsor', true );

	if ( empty( $sponsor_ids) ) {
		return false;
	}

	$sponsor_output = '<div class="wpp_episode_sponsors">';
	/**
	 * 1: Sponsor URL
	 * 2: Post Title (the_title)
	 * 3: Logo if available, Title if no Logo
	 * 4: Description (the_content)
	 */
	$format = '<div class="wpp-sponsor"><a href="%1$s" title="%2$s" target="_blank">%3$s</a> <p>%4$s</p></div>';
	$sponsors = new WP_Query( array( 'post_type' => 'sponsor', 'post__in' => $sponsor_ids ) );

	if ( $sponsors->have_posts() ) {
		while ( $sponsors->have_posts() ) {
			$sponsors->the_post();

			$sponsor_link = get_post_meta( get_the_id(), 'wpp_sponsor_link', true ); // @TODO: Check for link.

			$sponsor_link_content = ( has_post_thumbnail() ) ? get_the_post_thumbnail( get_the_id(), 'large' ) : get_the_title();

			$sponsor_output .= sprintf( $format,
				esc_url( $sponsor_link ),
				esc_attr( get_the_title() ),
				$sponsor_link_content,
				get_the_content()
			);
		}
		wp_reset_postdata();
	}

	return $sponsor_output . '</div>'; // Close the div we opened on L53.
}

/**
 * Print results of wpp_get_sponsors()
 */
function wpp_print_sponsors() {
	if ( ! empty( wpp_get_sponsors() ) ) {
		echo wpp_get_sponsors();
	}
}
