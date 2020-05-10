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
 * Generate HTML for displaying sponsors associated with episode.
 *
 * @return HTML string if there are sponsors, false if there are not.
 */
function wpp_get_sponsors( $episode_id = null, $include_title = true ) {
	global $post;
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  $post->ID;
	$sponsor_ids = get_post_meta( $episode_id, 'wpp_episode_sponsor', true );

	if ( empty( $sponsor_ids ) ) {
		return false;
	}

	$sponsor_output = '';

	if ( $include_title ) {
		$sponsor_output .= '<h4>' . esc_html__( 'Sponsored by:', 'wp-podcatcher' ) . '</h4>';
	}

	$sponsor_output .= '<div class="wpp-episode-sponsors">';
	/**
	 * 1: Sponsor URL
	 * 2: Post Title (the_title)
	 * 3: Logo if available, Title if no Logo
	 * 4: Description (the_content)
	 */
	$format = '<div class="wpp-sponsor"><a href="%1$s" title="%2$s" target="_blank">%3$s</a></div>';
	$sponsors = new WP_Query( array( 'post_type' => 'sponsor', 'post__in' => $sponsor_ids ) );

	if ( $sponsors->have_posts() ) {
		while ( $sponsors->have_posts() ) {
			$sponsors->the_post();

			$sponsor_link = get_post_meta( get_the_id(), 'wpp_sponsor_link', true ); // @TODO: Check for link.

			$sponsor_link_content = ( has_post_thumbnail() ) ? get_the_post_thumbnail( get_the_id(), 'wpp-big-square' ) : get_the_title();

			$sponsor_output .= sprintf( $format,
				esc_url( $sponsor_link ),
				esc_attr( get_the_title() ),
				$sponsor_link_content
			);
		}
		wp_reset_postdata();
	} else {
		return null;
	}

	return $sponsor_output . '</div>'; // Close the div we opened on L53.
}

/**
 * Print results of wpp_get_sponsors()
 *
 * @param $episode_id int ID of post to get sponsors.
 */
function wpp_print_sponsors( $episode_id = null, $include_title = true ) {
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$sponsors = wpp_get_sponsors( $episode_id, $include_title );
	if ( ! empty( $sponsors ) ) {
		echo $sponsors;
	}
}

/**
 * Get the most recent episode's ID
 */
function wpp_get_latest_episode() {
	$args = array(
		'posts_per_page' => 1,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'meta_key' => 'enclosure', // This is the meta key used by PowerPress.
	);

	$latest_episode = new WP_Query( $args );
	$post_ids = wp_list_pluck( $latest_episode->posts, 'ID' );
	return $post_ids[0];
}

/**
 * Get the next scheduled posts
 *
 * @param $posts_per_page int # of posts to display.
 */
function wpp_get_upcoming_episodes( $posts_per_page = 1 ) {
	$args = array(
		'posts_per_page' => $posts_per_page,
		'post_status' => 'future',
		'orderby' => 'post_date',
		'order' => 'ASC',
		'meta_key' => 'enclosure', // This is the meta key used by PowerPress.
	);

	$next_episodes = new WP_Query( $args );

	$episode_output = '<div class="wpp-upcoming-episodes">';
	/**
	 * 1: Episode Title
	 * 2: Time Stamp
	 * 3: Human Readable Date
	 */
	$format = '<div class="wpp-upcoming-episode"><h4>%1$s</h4><time datetime="%2$s">%3$s</time></div>';

	if ( $next_episodes->have_posts() ) {
		while ( $next_episodes->have_posts() ) {
			$next_episodes->the_post();

			$episode_output .= sprintf( $format,
				esc_attr( get_the_title() ),
				esc_attr( get_the_date( 'c' ) ),
				get_the_date()
			);
		}
		wp_reset_postdata();
	} else {
		$episode_output .= '<h4 class="wpp-no-schedule">There are no scheduled episodes right now.</h4>';
	}

	return $episode_output . '</div>'; // Close the div we opened on L113.
}

/**
 * Print the next scheduled posts
 *
 * @param $posts_per_page int # of posts to display.
 */
function wpp_print_upcoming_episodes( $posts_per_page = 1 ) {
	echo wpp_get_upcoming_episodes();
}

/**
 * Shortcode for next scheduled posts
 *
 * @param $atts Array
 * [wpp_schedule number="1"]
 */
function wpp_schedule_shortcode( $atts ) {
	$a = shortcode_atts( array(
		'number' => -1,
	), $atts );

	return wpp_get_upcoming_episodes( $a['number'] );
}

add_shortcode( 'wpp_schedule', 'wpp_schedule_shortcode' );

/**
 * Generate HTML for displaying transcript associated with episode.
 *
 * @return HTML string if there is transcript, false if there are not.
 */
function wpp_get_transcript( $episode_id = null ) {

	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$transcript_ids = get_post_meta( $episode_id, 'wpp_episode_transcript', true );

	if ( empty( $transcript_ids ) ) {
		return false;
	}

	$transcript_id = array_pop( $transcript_ids );

	$transcript = wpp_get_transcript_content( $episode_id );

	$format = '<div class="wpp-transcript">
		<a href="%s" class="wpp-transcript-link button alignright" title="episode transcript">View on separate page</a>
		<h2>Transcript</h2> 
		%s
	</div>';

	return sprintf( $format, get_permalink( $transcript_id ), $transcript );

}

function wpp_get_transcript_content( $episode_id = null ) {
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$transcript_ids = get_post_meta( $episode_id, 'wpp_episode_transcript', true );

	if ( empty( $transcript_ids ) ) {
		return false;
	}

	$transcript_id = array_pop( $transcript_ids );

	$transcript = get_post( $transcript_id );
	return $transcript->post_content;
}

/**
 * Generate HTML for displaying sponsors associated with episode feed.
 *
 * @return HTML string if there are sponsors, false if there are not.
 */
function wpp_get_sponsors_feed( $episode_id = null ) {
	global $post;
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  $post->ID;
	$sponsor_ids = get_post_meta( $episode_id, 'wpp_episode_sponsor', true );

	if ( empty( $sponsor_ids ) ) {
		return false;
	}


	$sponsor_output .= '<h5>' . esc_html__( 'Sponsored by:', 'wp-podcatcher' ) . '</h5><ul>';
	/**
	 * 1: Sponsor URL
	 * 2: Post Title (the_title)
	 * 3: Logo if available, Title if no Logo
	 * 4: Description (the_content)
	 */
	$format = '<li class="wpp-sponsor"><a href="%1$s" title="%2$s" target="_blank">%3$s</a>%4$s</li>';
	$sponsors = get_posts( array( 'post_type' => 'sponsor', 'post__in' => $sponsor_ids ) );

	if ( ! empty( $sponsors ) ) {
		foreach( $sponsors as $post ) {
			setup_postdata( $post );

			$sponsor_link = get_post_meta( get_the_id(), 'wpp_sponsor_link', true ); // @TODO: Check for link.

			$sponsor_link_content = get_the_title();

			$content = ( get_the_content() ) ? ': ' . get_the_content() : '';

			$sponsor_output .= sprintf( $format,
				esc_url( $sponsor_link ),
				esc_attr( get_the_title() ),
				$sponsor_link_content,
				$content
			);
		}
		wp_reset_postdata();
	} else {
		return null;
	}

	return $sponsor_output . '</ul>';
}

add_filter( 'content_save_pre', 'wpp_clean_google_docs' );

function wpp_clean_google_docs( $content ) {
	if ( ! ( 'transcript' == get_post_type() ) ) {
		return $content;
	} 

	$content = str_replace( '&nbsp;', '', $content );
	$content = str_replace( '</span>', '', $content );
	
	return preg_replace( '/<span[^>]+\>/i', '', $content );
}

/**
 * Automatically create a redirect when an episode number is saved for a post. 
 * Redirect is /episode-number/ => /post-slug/
 * Required Quick Redirects plugin
 */

add_action( 'acf/save_post', 'wpp_create_redirect' );

function wpp_create_redirect( $post_id ) {

	$episode_number = get_field( 'episode_number', $post_id );

	if ( ! isset( $episode_number ) ) {
		return;
	}

	$slug = '/'. $episode_number . '/';

	$attrs = array(
		'request_url'		=> $slug,
		'destination_url'	=> get_the_permalink( $post_id ),
		'newwindow'		=> 0,
		'nofollow'		=> 0,
	);
	
	$add_redirect = qppr_create_quick_redirect( $attrs );


	return $add_redirect;
}

/**
 * Automatically email guest when an episode is published. 
 * Attaches to acf/save_post in-case post is imeediately published,
 * and publish_future_post in the (more likely) event that it's scheduled.
 */

add_action( 'acf/save_post', 'wpp_email_guest' );
add_action( 'publish_future_post', 'wpp_email_guest', 10, 3 );


function wpp_email_guest( $post_id ) {

	if ( 'publish' !== get_post_status( $post_id ) ) {
		return;
	}

	$guest_email = get_field( 'guest_email', $post_id );
	$guest_name = get_field( 'guest_name', $post_id );
	$episode_number = get_field( 'episode_number', $post_id );

	if ( ! isset( $guest_email ) ) {
		return;
	}

	$format = 'Hey %1$s,

Thanks so much for coming on the show a while back. Your episode is live: %2$s/%3$s/

I’d love, and would appreciate,  if you could share it out.

Thanks again,

Joe';

	$subject = 'Your Episode is live!';
	$message = sprintf( $format, $guest_name, get_site_url(), $episode_number );
	$headers = 'From: jcasabona@gmail.com' . "\r\n" .
		'Reply-To:jcasabona@gmail.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	wp_mail( $guest_email, $subject, $message, $headers );
}

function wpp_get_media_URL() {
	if ( function_exists( 'powerpress_get_enclosure_data' ) ) {
		$episodeData = powerpress_get_enclosure_data( get_the_ID() );
		if ( ! empty( $episodeData['url'] ) ) {
			return $episodeData['url'];
		}
	}

	return false;
}