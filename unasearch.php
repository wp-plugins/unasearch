<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://unacode.com/unasearch
 * @since             0.1.0
 * @package           Unasearch
 *
 * @wordpress-plugin
 * Plugin Name:       Unasearch
 * Plugin URI:        http://unacode.com/unasearch/
 * Description:       Unasearch is a powerful plugin for improving content search on Wordpress
 * Version:           0.4.0
 * Author:            Unacode <vincent@unacode.com>
 * Author URI:        http://unacode.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       unasearch
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-unasearch-activator.php
 */
function activate_unasearch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-unasearch-activator.php';
  $activate = new Unasearch_Activator();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-unasearch-deactivator.php
 */
function deactivate_unasearch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-unasearch-deactivator.php';
	Unasearch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_unasearch' );
register_deactivation_hook( __FILE__, 'deactivate_unasearch' );
load_plugin_textdomain( 'unasearch', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-unasearch.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_unasearch() {

	$plugin = new Unasearch();
	$plugin->run();

}
run_unasearch();
