<?php

/**
 * @package    Preeco_Widgets
 * @subpackage Preeco_Widgets/admin
 * @author     preeco GmbH <info@preeco.de>
 */
class Preeco_Widgets_Admin {

	/**
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/preeco-widgets-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/preeco-widgets-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * @return void
	 * @since 1.1.0
	 */
	public function add_options_page() {
		add_options_page( 'preeco Widgets', 'preeco Widgets', 'manage_options',
			'preeco-widgets', [
				$this,
				'show_options_page'
			] );
	}

	/**
	 * @return void
	 * @since 1.0.0
	 */
	public function show_options_page() {
		require( dirname( __FILE__ ) . '/partials/preeco-widgets-admin-display.php' );
	}

	/**
	 * @return void
	 * @since 1.1.0
	 */
	public function register_settings() {
		register_setting( 'preeco-widgets-settings', 'preeco_widgets_timestamp' );
		register_setting( 'preeco-widgets-settings', 'preeco_widgets_caching_enabled' );
	}

	/**
	 * Add link to settings to plugin page
	 *
	 * @param array $links
	 *
	 * @return mixed
	 */
	public function plugin_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=preeco-widgets">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Activates the cron
	 * @return void
	 * @since 1.1.0
	 */
	public function update_cron_scheduler() {
		$cachingEnabled = filter_var( esc_attr( get_option( 'preeco_widgets_caching_enabled' ) ), FILTER_VALIDATE_BOOLEAN );
		if ( true === $cachingEnabled ) {
			if ( ! wp_next_scheduled( 'preeco_widgets_cache_all' ) ) {
				wp_schedule_event( time(), 'daily', 'preeco_widgets_cache_all' );
			}
		} else {
			wp_clear_scheduled_hook( 'preeco_widgets_cache_all' );
		}

	}

	public function cache_all_widgets() {
		$cachingEnabled = filter_var( esc_attr( get_option( 'preeco_widgets_caching_enabled' ) ), FILTER_VALIDATE_BOOLEAN );
		if ( $cachingEnabled ) {

			$cache_dir    = WP_CONTENT_DIR . '/preeco-cache/';
			$cached_files = array_diff( scandir( $cache_dir ), [ '.', '..' ] );
			foreach ( $cached_files as $cached_file ) {
				unlink( $cached_file );
			}

			$args  = array(
				'post_type'   => 'preeco_widgets',
				'post_status' => 'publish',
			);
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) : $query->the_post();
					$id = get_the_ID();
					if ( false !== $id ) {
						( new Preeco_Widgets_Content_Manager( $id ) )->cache();
					}
				endwhile;
			}

			wp_reset_postdata();
		}
	}

	public function update_options_updated_hook( $option_name, $value = null ) {
		if ( $option_name === 'preeco_widgets_caching_enabled' ) {
			$this->update_cron_scheduler();
		}
	}

	/**
	 * @return void
	 * @since 1.0.0
	 */
	public function register_preeco_posttype() {
		$labels = array(
			'name'                  => __( 'preeco Widgets', 'preeco_widgets' ),
			'singular_name'         => __( 'preeco Widget', 'preeco_widgets' ),
			'menu_name'             => __( 'preeco Widgets', 'preeco_widgets' ),
			'name_admin_bar'        => __( 'preeco Widget', 'preeco_widgets' ),
			'all_items'             => __( 'All Widgets', 'preeco_widgets' ),
			'add_new_item'          => __( 'Add New Widget', 'preeco_widgets' ),
			'add_new'               => __( 'Add New', 'preeco_widgets' ),
			'new_item'              => __( 'New Widget', 'preeco_widgets' ),
			'edit_item'             => __( 'Edit Widget', 'preeco_widgets' ),
			'update_item'           => __( 'Update Widget', 'preeco_widgets' ),
			'view_item'             => __( 'View Widget', 'preeco_widgets' ),
			'search_items'          => __( 'Search Widget', 'preeco_widgets' ),
			'not_found'             => __( 'Not found', 'preeco_widgets' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'preeco_widgets' ),
			'items_list'            => __( 'Widgets list', 'preeco_widgets' ),
			'items_list_navigation' => __( 'Widgets list navigation', 'preeco_widgets' ),
			'filter_items_list'     => __( 'Filter Widgets list', 'preeco_widgets' ),
		);

		$args = array(
			'label'               => __( 'preeco Widget', 'preeco_widgets' ),
			'description'         => __( 'Store your preeco Widget embed codes for easy access', 'preeco_widgets' ),
			'labels'              => $labels,
			'supports'            => array(
				'title',
				'editor',
				'author',
			),
			'taxonomies'          => array( 'post_tag' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 100,
			'menu_icon'           => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiB2aWV3Qm94PSIwIDAgMjQgMjQiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8ZyBpZD0iQXJ0Ym9hcmQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBmaWxsPSIjMURBOUI4IiBwb2ludHM9IjIyLjAyMTcyODUgMiAyMi4wMjE3Mjg1IDcgMjIgNyAyMiAyMiAyIDIyIDIgMTIgNyAxMiA3IDE2Ljk5OSAxNi45OTkgMTcgMTcgNyAyLjAyMTcyODUyIDcgMi4wMjE3Mjg1MiAyIj48L3BvbHlnb24+CiAgICA8L2c+Cjwvc3ZnPg==',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'show_in_rest'        => false,
		);

		register_post_type( 'preeco_widgets', $args );
	}

	/**
	 * @return void
	 * @since 1.0.0
	 */
	function attach_shortcode_column( $columns ) {
		unset(
			$columns['wpseo-score'],
			$columns['wpseo-title'],
			$columns['wpseo-metadesc'],
			$columns['wpseo-focuskw']
		);

		$columns = array_slice( $columns, 0, 2, true )
		           + array( 'shortcode' => __( 'Shortcode', 'preeco_widgets' ) )
		           + array_slice( $columns, 2, null, true );

		return $columns;
	}

	/**
	 * @return void
	 * @since 1.0.0
	 */
	function fill_shortcode_column( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode' :
				echo "<pre>[preeco-widget id={$post_id}]</pre>";
				break;
		}
	}

	/**
	 * @return void
	 * @since 1.0.0
	 */
	function disable_gutenberg_for_preeco_widgets( $default ) {
		global $post;
		if ( $post->post_type === 'preeco_widgets' ) {
			return false;
		}

		return $default;
	}
}
