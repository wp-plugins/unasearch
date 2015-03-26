<?php

/**
 * Fired when settings options are save
 *
 * @link       http://unacode.com/unasearch
 * @since      0.4.0
 *
 * @package    Unasearch
 * @subpackage Unasearch/includes
 */

/**
 * Fired when settings options are save
 *
 * @since      0.4.0
 * @package    Unasearch
 * @subpackage Unasearch/includes
 * @author     Vincent Bocquet <support@unacode.com>
 */
class Unasearch_Index {

  /**
   * The unique identifier of this plugin.
   *
   * @since    0.4.0
   * @access   protected
   * @var      string    $plugin_name    The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * All the post types to index
   *
   * @since    0.4.0
   * @access   protected
   * @var      array    $post_types    The post types to index for the search
   */
  protected $post_types;

  /**
   * All posts to index
   *
   * @since    0.4.0
   * @access   protected
   * @var      array    $posts    The posts to index
   */
  protected $posts;



  /**
   * Short Description. (use period)
   *
   * @since    0.4.0
   */
  public function __construct() {

    $this->plugin_name = 'unasearch';

  }

  /**
   * Launch index processing
   *
   * @since    0.4.0
   */
  public function launch_index( $post_id = false ) {

    $this->post_types = $this->get_includes_post_types();

    if( $post_id == false ) {
      $this->remove_all_terms();
      $this->posts = $this->get_posts();
      $this->index_terms();

    } else {
      $post_id = intval($post_id);

      if( $this->is_to_index($post_id) ){
        $this->remove_all_terms($post_id);
        $this->index_terms($post_id);
      }
    }

  }

  /**
   * Check if the post is to index
   *
   * @since    0.4.0
   */
  public function is_to_index( $post_id = false ) {

    if( !is_int($post_id) || !is_array($this->post_types) ) {
      return false;
    }

    if( !get_post_type($post_id) || !in_array(get_post_type($post_id), $this->post_types) ) {
      return false;
    }

    return true;
  }

  /**
   * Remove all the terms index from database
   *
   * @since    0.4.0
   */
  public function remove_all_terms( $post_id = false ) {

    global $wpdb;

    if( $post_id == false ) {
      $wpdb->query('TRUNCATE TABLE `'.$wpdb->prefix.'una_index`');
      $wpdb->query('TRUNCATE TABLE `'.$wpdb->prefix.'una_terms`');
    
    } else {
     
      $sql = "DELETE FROM ".$wpdb->prefix."una_index WHERE post_id = %d";
      $result = $wpdb->query( $wpdb->prepare("$sql", $post_id) );
      
      return $result;
    }

  }

  /**
   * Index all the terms
   *
   * @since    0.4.0
   */
  public function index_terms( $post_id = false ) {
    
    if( $post_id == false ) {

      foreach ($this->posts as $k => $v) {
        $this->index_content( $v->post_title, $v->ID, 1 );
        $this->index_content( wp_strip_all_tags($v->post_content), $v->ID, 2 );
      }

    } else {
        $the_post = get_post($post_id);

        if( $the_post != NULL ) {
          $this->index_content( $the_post->post_title, $post_id, 1 );
          $this->index_content( wp_strip_all_tags($the_post->post_content), $post_id, 2 );
        }

    }

  }

  /**
   * Get all the post types to index
   *
   * @since    0.4.0
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
   * Get all posts to index
   *
   * @since    0.4.0
   */
  private function get_posts() {
    $args = array(
      'posts_per_page' => -1,
      'offset'=> 1,
      'post_type' => $this->post_types
    );

    $posts = get_posts( $args );

    return $posts;
  }

  /**
   * Set a string to lowercase
   *
   * @since    0.4.0
   */
  private function set_to_lower_case($string) {
    $string = strtolower(trim($string));
    return $string;
  }

  /**
   * Clean whitespace in a string
   *
   * @since    0.4.0
   */
  private function clean_whitespaces($string) {
    $string = preg_replace('!\s+!', ' ', $string);
    return $string;
  }

  /**
   * Get a array of terms from a term
   *
   * @since    0.4.0
   */
  private function get_array_terms($string) {
    $terms = explode(" ", $string);
    return $terms;
  }

  /**
   * Set a terms array in database
   *
   * @since    0.4.0
   */
  private function set_terms($terms) {
    if( !is_array($terms) && count($terms) < 1 ) {
      return false;
    }
    global $wpdb;
    $place_holders = array();
    
    foreach( $terms as $k => $v ) {
      $place_holders[] = "('%s')";
    }

    $sql = "INSERT IGNORE INTO ".$wpdb->prefix."una_terms (term) VALUES ";
    $sql .= implode(', ', $place_holders);
    $result = $wpdb->query( $wpdb->prepare("$sql ", $terms) );

    return $result;
  }

  /**
   * Remove double from a array
   *
   * @since 0.4.0
   *
   * @param array $array Source array need to remove double entries
   *
   * @return array|false Array without double
   */
  private function remove_double($array) {
    if ( is_array($array) ) {
      $array = array_unique($array);
    } else {
      return false;
    }
    return $array;
  }

  /**
   * Get terms count with terms id
   *
   * @since 0.4.0
   *
   * @param array $terms Terms
   *
   * @return array Terms id with count
   */
  function get_terms_count($terms) {
    global $wpdb;
    $place_holders = array();

    $sql = "SELECT * FROM ".$wpdb->prefix."una_terms WHERE term IN (";
  
    foreach( $terms as $k => $v ) {
      $place_holders[] = "'%s'";
    }

    $sql .= implode(', ', $place_holders);
    $sql .= ");";

    $result = $wpdb->get_results( $wpdb->prepare("$sql ", $terms), 'ARRAY_A' );
    $counter = array_count_values($terms);
    $return = array();

    foreach ($result as $k => $v) {

      if( array_key_exists($v['term'], $counter) ) {
        $item_count = $counter[$v['term']];
        $item_term = $v['id'];
        $return[$item_term] = $item_count;
      }

    }

    return $return;
  }

  /**
   * Set indexes terms in database
   *
   * @since 0.4.0
   *
   * @param array $terms_id Term's id to index
   * @param int   $post_id  Post id
   * @param int   $type_id  Type of the content
   *
   * @return bool Statut of db request
   */
  function set_indexes_terms($terms_id, $post_id, $type_id){
    global $wpdb;
    $place_holders = array();
    $values = array();
    
    foreach( $terms_id as $k => $v ) {
      $place_holders[] = "('%d', '%d', '%d', '%d')";
      array_push($values, $k, $post_id, $type_id, $v);
    }

    $sql = "INSERT INTO ".$wpdb->prefix."una_index (term_id, post_id, type_id, count) VALUES ";
    $sql .= implode(', ', $place_holders);
    $result = $wpdb->query( $wpdb->prepare("$sql ", $values) );

    return $result;

  }

  /**
   * Index content post terms
   *
   * @since    0.4.0
   */
  private function index_content($string, $id, $type_id) {
    $string    = $this->set_to_lower_case($string);
    $string    = $this->clean_whitespaces($string);
    $terms     = $this->get_array_terms($string);
    $set_terms = $this->set_terms($terms);
    $terms_ids = $this->get_terms_count($terms);
    $terms     = $this->remove_double($terms);
    $this->set_indexes_terms($terms_ids, $id, $type_id);
  }



}