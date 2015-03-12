<?php

/**
 *
 * @package    Unasearch
 * @author     Vincent Bocquet <support@unacode.com>
 */

class Unasearch_Engine {

  /**
   * The ID of this plugin.
   *
   * @since    0.1.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * Initialize the class and set its properties.
   *
   * @since    0.1.0
   * @var      string    $plugin_name       The name of this plugin.
   */
  public function __construct( $plugin_name ) {

    $this->plugin_name = $plugin_name;
    add_filter( 'pre_get_posts', array( $this, 'alter_search_loop' ) );
  }

  /**
   * Alter search loop with options
   *
   * @since    0.1.0
   */
  public function alter_search_loop($query) {

    if ( !is_admin() && $query->is_main_query() && $query->is_search ) {

      $post_types = $this->get_includes_post_types();
      
      if ( ($post_types != false) && is_array($post_types) ) {
        $query->set('post_type', $post_types);
      }

      $orderby = $this->get_orderby();
      
      if ( ($orderby != false) ) {

        $query->set('orderby', $orderby );
      }

    }

    return $query;
  }

  /**
   * Get all post type to include in search
   *
   * @since    0.1.0
   */
  private function get_includes_post_types() {
    $return = array();
    $options = get_option('unasearch_settings');

    if( is_array($options) && array_key_exists('post_types', $options) && is_array($options['post_types']) ){

      foreach ( $options['post_types'] as $k => $v ) { 
      
        if( ($v != 'revision') && ($v != 'nav_menu_item') && ($v != 'attachment') ){
          $return[] = $v;
        }
      
      }
    }

    return $return;
  }

  /**
   * Get the orderby to alter the search
   *
   * @since    0.1.0
   */
  private function get_orderby() {
    $options = get_option('unasearch_settings');

    if( is_array($options) && array_key_exists('orderby', $options) && (trim($options['orderby']) != '' ) ){
      return $options['orderby'];
    } else {
      return false;
    }

  }

}
