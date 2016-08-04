<?php
/**
 *  WPP_Sponsors registers the episode post type
 *
 * @package wp_podcatcher
 */

/**
 * WPP_Sponsors registers the episode post type
 *
 * @package wp_podcatcher
 **/
class WPP_Sponsors {

	/**
	 * Slug of our CPT
	 *
	 * @var string
	 */
	public $name = 'sponsor';

	/**
	 * Plural Slug of our CPT
	 *
	 * @var string
	 */
	public $plural_name = 'sponsors';

	/**
	 * Our construct
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
	}


	/**
	 * Wrapper for register_post_type
	 */
	public function register_cpt() {

		$single_label = 'Sponsor';
		$plural_label = 'Sponsors';

		/**
		 * Registers a new post type
		 *
		 * @uses $wp_post_types Inserts new post type object into the list
		 *
		 * @param string  Post type key, must not exceed 20 characters
		 * @param array|string  See optional args description above.
		 * @return object|WP_Error the registered post type object, or an error object
		 */
		register_post_type( $this->name, array(
			'labels'              => array(
				'name'                => __( $plural_label, 'wp-podcatcher' ),
				'singular_name'       => __( $single_label, 'wp-podcatcher' ),
				'add_new'             => __( 'Add a New ' . $single_label, 'wp-podcatcher' ),
				'add_new_item'        => __( 'Add a New ' . $single_label, 'wp-podcatcher' ),
				'edit_item'           => __( 'Edit ' . $single_label, 'wp-podcatcher' ),
				'new_item'            => __( 'New ' . $single_label, 'wp-podcatcher' ),
				'view_item'           => __( 'View ' . $single_label, 'wp-podcatcher' ),
				'search_items'        => __( 'Search ' . $plural_label, 'wp-podcatcher' ),
				'not_found'           => __( 'No '. $plural_label .' found', 'wp-podcatcher' ),
				'not_found_in_trash'  => __( 'No '. $plural_label .' found in Trash', 'wp-podcatcher' ),
				'parent_item_colon'   => __( 'Parent '. $single_label .':', 'wp-podcatcher' ),
				'menu_name'           => __( $plural_label, 'wp-podcatcher' ),
			),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-id',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array( 'title', 'thumbnail', 'revisions', 'comments' ),
		) );

	}
} // END class

new WPP_Sponsors();
