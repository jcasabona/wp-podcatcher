<?php
/**
 *  WPP_PowerPress_Sponsors registers the metabox for associating sponsors with posts
 *
 * @package wp_podcatcher
 */
class WPP_Episode_Transcripts{

	/**
	 * Our construct
	 */
	public function __construct() {
		 add_action( 'fm_post', array( $this, 'fm_setup' ) );
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {

		$fm = new Fieldmanager_Autocomplete( array(
			'name' => 'wpp_episode_transcript',
			'limit'          => 0,
			'sortable'       => true,
			'show_edit_link' => true,
			'datasource' => new Fieldmanager_Datasource_Post( array(
				'query_args' => array( 'post_type' => 'transcript', 'limit' => 1 ),
			) ),
		) );

		$fm->add_meta_box( 'Episode Transcript', 'post' );
	}
} // END class

new WPP_Episode_Transcripts();
