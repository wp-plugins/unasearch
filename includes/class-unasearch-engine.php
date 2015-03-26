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

    if ( !is_admin() && $query->is_main_query() && $query->is_search() ) {
      global $wpdb;
      $results = array();
      $search_string = trim($_GET['s']);
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

      $sql = new Unasearch_Query_Builder($search_string, $paged);

      if( $sql->query != false ) {
       
        $query_results = $wpdb->get_results( $wpdb->prepare("$sql->query ", $sql->values), 'ARRAY_A' );

        foreach ($query_results as $k => $v) {
          $results[] = $v['ID'];
        }
      }

      $query->init();
      $query->set('post_type', 'any');
      $query->set('post_status', 'any');
      $query->set('orderby', 'post__in');

      if( is_array($results) && count($results) > 0 ) {
        $query->set('posts_per_page', count($results));
        $query->set('post__in', $results);
      
      } else {
        $query->set('post__in', array(0));
      }
    }

    return $query;
  }

}