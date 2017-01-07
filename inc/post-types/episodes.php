<?php
/**
 *  WPP_Episodes registers the episode post type
 *
 * @package wp_podcatcher
 */
class WPP_Episodes extends WP_Podcatcher {

	/**
	 * Our construct
	 */
	public function __construct() {
		$this->name = 'episode';
		$this->plural_name = 'episodes';
		$this->icon .= 'microphone';
		 parent::__construct();
		 add_action( 'fm_post_' . $this->name, array( $this, 'fm_setup' ) );
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {
		$fm = new Fieldmanager_Group( array(
			'name' => 'wpp_audio_group',
			'children' => array(
				'wpp_audio_file' => new Fieldmanager_Media( array(
					'name' => 'wpp_audio_file',
					'button_label' => 'Add Audio File',
					'modal_title' => 'Select Audio File',
					'modal_button_label' => 'Use this File',
					'preview_size' => 'icon',
				) ),
				'wpp_audio_link' => new Fieldmanager_TextField( 'or Audio File Link'),
				// 'wpp_audio_size' => new Fieldmanager_TextField( 'Size in Bytes' ),
				// 'wpp_audio_time' => new Fieldmanager_TextField( 'Duration' ),
			),
		) );
		$fm->add_meta_box( 'Audio Information', array( $this->name ) );

		// @TODO: Add filter to make sure only audio is uploaded.

		$fm = new Fieldmanager_Autocomplete( array(
			'name' => 'wpp_episode_sponsor',
			'limit'          => 0,
			'add_more_label' => 'Add another Sponsor',
			'sortable'       => true,
			'show_edit_link' => true,
			'datasource' => new Fieldmanager_Datasource_Post( array(
				'query_args' => array( 'post_type' => 'sponsor', 'limit' => 2 ),
			) ),
		) );

		$fm->add_meta_box( 'Episode Sponsor', $this->name );
	}
} // END class

new WPP_Episodes();
