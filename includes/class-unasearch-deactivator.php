<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://unacode.com/unasearch
 * @since      0.1.0
 *
 * @package    Unasearch
 * @subpackage Unasearch/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Unasearch
 * @subpackage Unasearch/includes
 * @author     Vincent Bocquet <support@unacode.com>
 */
class Unasearch_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {

    // delete option
    delete_option( 'unasearch_settings' );
	}

}
