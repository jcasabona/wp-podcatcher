<?php
/**
 * A general functions file
 *
 * @package wp-podcatcher
 */


// Start Your Engines.
require_once( 'post-types/parent-class.php' );

function wpp_get_sponsor_link( $spon_id, $episode_id = NULL ) {
	$orig_link = get_post_meta( $spon_id, 'wpp_sponsor_link', true );
	
	if ( is_null( $episode_id ) || is_null( $spon_id ) ) {
		return $orig_link;
	}

	$alt_links = get_fields( $spon_id );

	if ( false == $alt_links ) {
		return $orig_link;
	}

	$alt_links = $alt_links['alternate_links'];

	if( ! is_array( $alt_links ) ) {
		return $orig_link;
	}

	if ( false === array_search( $episode_id, array_column( $alt_links, 'episode' ) ) ) {
		return $orig_link;
	}
	foreach ( $alt_links as $alt ) {
		if ( $alt['episode']  == $episode_id ) {
			return $alt['alt_link'];
		}
	}

	return $orig_link;
}

/**
 * Generate HTML for displaying sponsors associated with episode.
 *
 * @return HTML string if there are sponsors, false if there are not.
 */
function wpp_get_sponsors( $episode_id = null, $include_title = true ) {
	global $post;
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  $post->ID;
	$sponsor_ids = get_post_meta( $episode_id, 'hibi_episode_sponsor', true );

	if ( empty( $sponsor_ids ) ) {
		return false;
	}

	$sponsor_output = '<section class="wpp-sponsors">';

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
	$format = '<div class="wpp-sponsor"><a href="%1$s" title="%2$s" rel="sponsored" target="_blank">%3$s</a></div>';
	$sponsors = new WP_Query( array( 'post_type' => 'sponsor', 'post__in' => $sponsor_ids ) );

	if ( $sponsors->have_posts() ) {
		while ( $sponsors->have_posts() ) {
			$sponsors->the_post();


			$sponsor_link = wpp_get_sponsor_link( get_the_id(), $episode_id );

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

	return $sponsor_output . '</div></section>'; // Close the div we opened on L51.
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
		'post_type' => array( 'post' ),
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
		'post_type' => array( 'post', 'podcast' ),
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
 * Shortcode for current sponsors
 *
 */

function wpp_sponsor_shortcode( $atts ) {

	return wpp_get_sponsors();
}

add_shortcode( 'current_sponsors', 'wpp_sponsor_shortcode' );



/**
 * Generate HTML for displaying  associated with episode.
 *
 * @return HTML string if there is transcript, false if there are not.
 */
function wpp_get_transcript( $episode_id = null ) {

	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$transcript_id = get_post_meta( $episode_id, 'hibi_transcript', true );

	if ( empty( $transcript_id ) || ! is_singular() ) {
		return false;
	}

	$transcript_id = wpp_clean_transcript_id( $transcript_id );

	$transcript = wpp_get_transcript_content( $episode_id );
	
	// Hey this currently depends on the Genesis Blocks plugin - specifically the accordion block.
	$format = '<div class="wp-block-genesis-blocks-gb-accordion gb-block-accordion wpp-transcript">
		<details>
			<summary class="gb-accordion-title">Transcript</summary>
			<div class="gb-accordion-text">%s</div>
		</details>
	</div>';

	return sprintf( $format, $transcript );

}

function wpp_get_transcript_content( $episode_id = null ) {
	if( is_feed() ) return false;
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$transcript_id = get_post_meta( $episode_id, 'hibi_transcript', true );

	if ( empty( $transcript_id ) ) {
		echo '<!--empty man-->';
		return false;
	}

	$transcript_id = wpp_clean_transcript_id( $transcript_id );

	$transcript = get_post( $transcript_id );
	return  wpautop( $transcript->post_content, true );
}

function convert_wpp_to_hibi() {
	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'post',
	);
	$the_query = new WP_Query( $args );

	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$old_t = get_post_meta( get_the_ID(), 'wpp_episode_transcript', true );
		$old_s = get_post_meta( get_the_ID(), 'wpp_episode_sponsor', true );

		update_field( 'hibi_episode_sponsor', $old_s, get_the_ID() );
		update_field( 'hibi_transcript', $old_t, get_the_ID() );
	}

	wp_reset_postdata();
}

//add_action( 'admin_init', 'convert_wpp_to_hibi' );

function wpp_clean_transcript_id( $transcript_id ) {
	return ( is_array( $transcript_id ) ) ? $transcript_id[0] : $transcript_id;
}

/**
 * Generate HTML for displaying sponsors associated with episode feed.
 *
 * @return HTML string if there are sponsors, false if there are not.
 */
function wpp_get_sponsors_feed( $episode_id = null ) {
	global $post;
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  $post->ID;
	$sponsor_ids = get_post_meta( $episode_id, 'hibi_episode_sponsor', true );

	if ( empty( $sponsor_ids ) ) {
		return false;
	}


	$sponsor_output .= '<p><b>' . esc_html__( 'Sponsored by:', 'wp-podcatcher' ) . '</b> ';
	/**
	 * 1: Sponsor URL
	 * 2: Post Title (the_title)
	 * 3: Logo if available, Title if no Logo
	 * 4: Description (the_content)
	 */
	$format = '<a href="%1$s" title="%2$s" rel="sponsored" target="_blank">%3$s</a> | ';
	$sponsors = get_posts( array( 'post_type' => 'sponsor', 'post__in' => $sponsor_ids ) );

	if ( ! empty( $sponsors ) ) {
		foreach( $sponsors as $post ) {
			setup_postdata( $post );

			$sponsor_link = wpp_get_sponsor_link( get_the_id(), $episode_id );
			$sponsor_link_content = get_the_title();

			//$content = ( get_the_content() ) ? ': ' . get_the_content() : '';

			$sponsor_output .= sprintf( $format,
				esc_url( $sponsor_link ),
				esc_attr( get_the_title() ),
				$sponsor_link_content,
				//$content
			);
		}
		wp_reset_postdata();
	} else {
		return null;
	}

	return substr($sponsor_output, 0, strlen($sponsor_output) - 3) . '</p>';
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

	if ( 'post' != get_post_type( $post_id ) ) {
		error_log( "Not a post Post Type" );
		return;
	}

	$episode_number = get_field( 'episode_number', $post_id );

	if ( empty( $episode_number ) ) {
		error_log( "No episode number." );
		return;
	}

	error_log( get_post_status( $post_id ) );

	if ( 'publish' == get_post_status( $post_id ) || 'future' == get_post_status( $post_id ) ) {
		error_log( "Creating redirect." );

		$slug = '/'. $episode_number;

		// @TODO: Fix duplicate redirect creation. 
		$redirect_info = array(
			'url'         => $slug,
			'action_data' => array( 'url' => get_the_permalink( $post_id ) ),
			'regex'       => false,
			'group_id'    => 1,
			'match_type'  => 'url',
			'action_type' => 'url',
			'action_code' => 301,
		);

		Red_Item::create( $redirect_info );
	}

	return;
}

/**
 * Automatically email guest when an episode is published. 
 * Attaches to acf/save_post in-case post is imeediately published,
 * and publish_future_post in the (more likely) event that it's scheduled.
 */

//add_action( 'acf/save_post', 'wpp_email_guest' );
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

function wpp_get_media_URL( $id = null ) {
	if ( function_exists( 'ss_get_podcast' ) ) {
		$id = $id ?? get_the_id();
		return get_post_meta( $id, 'audio_file', true );
	}

	return false;
}

function wpp_check_environment( $user_id ) {
	switch ( wp_get_environment_type() ) {
		case 'local':
			$admin_color = 'sunrise';
			break;
		case 'development':
			$admin_color = 'sunrise';
			break;
		case 'staging':
			$admin_color = 'blue';
			break;
		default: 
			$admin_color = get_user_option( 'admin_color' );
	}

	$args = array(
		'ID' => get_current_user_id(),
		'admin_color' => $admin_color,
	);

	wp_update_user( $args );
}
// add_action( 'admin_init', 'wpp_check_environment' );

function wpp_sponsor_count() {
	$args = array(
		'posts_per_page' => -1,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'post_type' => array( 'post' ),
		'category__not_in' => array( 510 ),
	);

	$posts = new WP_Query( $args );
	$output = '';
	$format = '<tr><td>%4$s</td><td><a href="%2$s">%1$s</a></td><td class="%5$s">%3$s</td></tr>';
	if ( $posts->have_posts() ) {
		$output = '<table><thead><th>Published</th><th>Title</th><th>Available Spots</th></thead><tbody>';
		while ( $posts->have_posts() ) {
			$posts->the_post();
			$spons = get_post_meta( get_the_id(), 'hibi_episode_sponsor', true );
			
			$count = 3 - count($spons);
			
			$class = ( $count <= 1 ) ? 'one_left' : 'more_left';
			
			if ( is_array( $spons ) && count($spons) < 3 ) {
				$output .= sprintf( $format,
					esc_attr( get_the_title() ),
					esc_url( get_the_permalink() ),
					$count,
					get_the_date( 'Y-m-d' ),
					$class
				);
			}
		}
		wp_reset_postdata();
	} else {
		return null;
	}

	$output .= '</tbody></table>';
	return $output;
}

add_shortcode( 'spon_count', 'wpp_sponsor_count' );