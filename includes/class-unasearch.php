<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://unacode.com/unasearch
 * @since      0.1.0
 *
 * @package    Unasearch
 * @subpackage Unasearch/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Unasearch
 * @subpackage Unasearch/includes
 * @author     Vincent Bocquet <support@unacode.com>
 */
class Unasearch {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Unasearch_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {

		$this->plugin_name = 'unasearch';
		$this->version = '0.1.0';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->load_settings();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Unasearch_Loader. Orchestrates the hooks of the plugin.
	 * - Unasearch_i18n. Defines internationalization functionality.
	 * - Unasearch_Admin. Defines all hooks for the dashboard.
	 * - Unasearch_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-unasearch-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-unasearch-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-unasearch-querybuilder.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-unasearch-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-unasearch-settings.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-unasearch-index.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-unasearch-engine.php';


		$this->loader = new Unasearch_Loader();

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Unasearch_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_name . '.php' );

		$plugin_index = new Unasearch_Index( $this->get_plugin_name() );

		// Launch index terms
		if( isset( $_POST['index_terms'] ) ) {
			$plugin_index->launch_index();
		}

		$this->loader->add_action( 'save_post', $plugin_index, 'launch_index' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Unasearch_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Build engine
		$plugin_engine = new Unasearch_Engine( $this->get_plugin_name() );
	}

	/**
	 * Load the settings workflow
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_settings() {

		$plugin_settings = new Unasearch_Settings( $this->get_plugin_name() );

		// Register settigns
		$this->loader->add_action( 'admin_init' , $plugin_settings, 'register_settings' );
		
		// Add the options page and menucitem.
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'add_settings_admin' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Unasearch_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get an option
	 *
	 * Looks to see if the specified setting exists, returns default if not.
	 *
	 * @since 	0.0.1
	 * @return 	mixed 	$value 	Value saved / $default if key if not exist
	 */
	static public function get_option( $key, $default = false ) {
		if ( empty( $key ) ) {
			return $default;
		}

		$plugin_options = get_option( 'unsearch_settings', array() );

		if ( !is_array( $plugin_options ) ) {
			return $default;
		}

		$value = isset( $plugin_options[ $key ] ) ? $plugin_options[ $key ] : $default;
		return $value;
	}

}