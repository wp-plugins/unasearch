<?php

/**
 * Fired during plugin activation
 *
 * @link       http://unacode.com/unasearch
 * @since      0.1.0
 *
 * @package    Unasearch
 * @subpackage Unasearch/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Unasearch
 * @subpackage Unasearch/includes
 * @author     Vincent Bocquet <support@unacode.com>
 */
class Unasearch_Activator {

  /**
   * Initialize the class and set its properties.
   */
  public function __construct() {

    $this->add_tables();
    $this->add_types();
    $this->register_options();

  }

  /**
   * Add table in database
   */
  public function add_tables() {

    global $wpdb;
    $wpdb->hide_errors();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $this->get_schema() );
    
  }

  /**
   * Get table shema
   */
  public function get_schema() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    return "
    CREATE TABLE {$wpdb->prefix}una_terms (
      id bigint(20) unsigned NOT NULL auto_increment,
      term varchar(128) NOT NULL,
      PRIMARY KEY (id),
      UNIQUE KEY (term)
    ) $charset_collate;

    CREATE TABLE {$wpdb->prefix}una_index (
      id bigint(20) unsigned NOT NULL auto_increment,
      term_id bigint(20) NOT NULL,
      post_id bigint(20) NOT NULL,
      type_id bigint(20) NOT NULL,
      count bigint(20) NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;

    CREATE TABLE {$wpdb->prefix}una_type (
      id bigint(20) NOT NULL auto_increment,
      type varchar(256) NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;
    ";
  }

  /**
   * Insert types in database una_type
   */
  public function add_types() {

    global $wpdb;
    $types = array('title', 'content');

    foreach( $types as $k => $v ) {
      $wpdb->insert( $wpdb->prefix.'una_type', array(
        'type'        => $v
      ) );
    }

  }

  /**
   * Register base options values
   */
  public function register_options() {
    global $wpdb;
    $options = array();
    $options['post_status'] = array('publish' => 'publish');
    $options['order']       = 'DESC';
    $options['orderby']     = 'post_date';
    $options['post_types']  = array('post' => 'post');

    update_option( 'unasearch_settings', $options );

  }

}
