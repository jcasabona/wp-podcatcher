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
 * Shortcode for current sponsors
 *
 */

function wpp_sponsor_shortcode( $atts ) {

	return wpp_get_sponsors( wpp_get_latest_episode(), false );
}

add_shortcode( 'current_sponsors', 'wpp_sponsor_shortcode' );

function wpp_episode_sponsor_shortcode( $atts ) {

	return wpp_get_sponsors();
}

add_shortcode( 'episode_sponsors', 'wpp_episode_sponsor_shortcode' );

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
 * When Podcast Importer imports an episode, save the episode number
 * (parsed from <itunes:episode> in the feed) directly as post meta and
 * create the /NNN → permalink redirect. No title parsing needed — this
 * fires after wp_insert_post() and after all importer meta is written.
 *
 * Hook source: podcast-importer-secondline/app/Helper/Importer/FeedItem.php
 */
add_action( 'podcast_importer_secondline_feed_item_imported', function( $feed_item ) {
	if ( ! is_object( $feed_item ) || empty( $feed_item->current_post_id ) ) {
		return;
	}

	$post_id        = $feed_item->current_post_id;
	$episode_number = isset( $feed_item->episode_number ) ? trim( (string) $feed_item->episode_number ) : '';

	if ( '' === $episode_number ) {
		error_log( "🚫 [podcatcher] No <itunes:episode> on imported post {$post_id}" );
		return;
	}

	// Stored as plain meta; ACF reads the same key.
	update_post_meta( $post_id, 'episode_number', $episode_number );

	wpp_maybe_create_redirect( $post_id, $episode_number );

	error_log( "✅ [podcatcher] Saved episode_number {$episode_number} and redirect for post {$post_id}" );
}, 20 );

/**
 * Create a redirect from /NNN → permalink for a given episode number.
 * Bails if Redirection plugin isn't loaded or a redirect already exists.
 * Requires the Redirection plugin.
 */
function wpp_maybe_create_redirect( $post_id, $episode_number ) {
	if ( empty( $episode_number ) ) return;

	$slug = '/' . $episode_number;

	global $wpdb;
	$existing = $wpdb->get_var( $wpdb->prepare(
		"SELECT id FROM {$wpdb->prefix}redirection_items WHERE url = %s",
		$slug
	));

	if (!$existing) {
		if ( ! class_exists( 'Red_Item' ) ) {
			error_log( "⚠️ Redirection plugin not loaded — skipping redirect for {$slug}" );
			return;
		}

		Red_Item::create([
			'url'         => $slug,
			'action_data' => [ 'url' => get_permalink( $post_id ) ],
			'regex'       => false,
			'group_id'    => 1,
			'match_type'  => 'url',
			'action_type' => 'url',
			'action_code' => 301,
		]);

		error_log("🔁 Created redirect from {$slug} → " . get_permalink($post_id));
	} else {
		error_log("↩️ Redirect for {$slug} already exists.");
	}
}

/**
 * Automatically email guest when an episode is published. 
 * Attaches to acf/save_post in-case post is imeediately published,
 * and publish_future_post in the (more likely) event that it's scheduled.
 */

//add_action( 'acf/save_post', 'wpp_email_guest' );
//add_action( 'publish_future_post', 'wpp_email_guest', 10, 3 );


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
