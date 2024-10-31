<?php

/**
 * @since      1.0.0
 * @package    Preeco_Widgets
 * @subpackage Preeco_Widgets/includes
 * @author     preeco GmbH <info@preeco.de>
 */
class Preeco_Widgets {

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      Preeco_Widgets_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PREECO_WIDGETS_VERSION' ) ) {
			$this->version = PREECO_WIDGETS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'preeco-widgets';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-preeco-widgets-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-preeco-widgets-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-preeco-widgets-content-manager.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-preeco-widgets-admin.php';

		$this->loader = new Preeco_Widgets_Loader();
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Preeco_Widgets_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Preeco_Widgets_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_preeco_posttype' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'added_option', $plugin_admin, 'update_options_updated_hook', 10, 2 );
		$this->loader->add_action( 'updated_option', $plugin_admin, 'update_options_updated_hook', 10, 3 );

		$this->loader->add_filter( 'user_can_richedit', $plugin_admin, 'disable_gutenberg_for_preeco_widgets' );
		$this->loader->add_filter( 'manage_preeco_widgets_posts_columns', $plugin_admin, 'attach_shortcode_column' );
		$this->loader->add_filter( 'manage_preeco_widgets_posts_custom_column', $plugin_admin, 'fill_shortcode_column', 10, 2 );

		// add settings link to plugin list
		$baseName = plugin_basename( realpath( dirname( __FILE__ ) . '/../preeco-widgets.php' ) );
		$this->loader->add_filter( 'plugin_action_links_' . $baseName, $plugin_admin, 'plugin_add_settings_link' );

		// cron
		$this->loader->add_action( 'preeco_widgets_cache_all', $plugin_admin, 'cache_all_widgets' );
	}

	/**
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
		$this->register_preeco_shortcode();
	}

	/**
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * @return    Preeco_Widgets_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * @return    string
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @return    void
	 * @since     1.0.0
	 */
	function register_preeco_shortcode() {
		add_shortcode( 'preeco-widget', function ( $atts ) {
			$atts = shortcode_atts( array( 'id' => 'id' ), $atts );

			return ( new Preeco_Widgets_Content_Manager( $atts['id'] ) )->render();
		} );
	}

}
