<?php

/**
 * Generate a MySQL query base on some parameters
 *
 * @package    Unasearch
 * @subpackage Unasearch/includes
 * @author     Vincent Bocquet <support@unacode.com>
 */
class Unasearch_Query_Builder {

	/**
	 * The Mysql query, in string
	 *
	 * @since    0.4.0
	 */
	public $query;

	/**
	 * The user search request
	 *
	 * @since    0.4.0
	 */
	public $search_string;

	/**
	 * The user search terms
	 *
	 * @since    0.4.0
	 */
	public $search_terms;

	/**
	 * The order setting
	 *
	 * @since    0.4.0
	 */
	public $order;

	/**
	 * The orderby setting
	 *
	 * @since    0.4.0
	 */
	protected $orderby;

	/**
	 * The post_status setting
	 *
	 * @since    0.4.0
	 */
	public $post_status;

	/**
	 * The post_type setting
	 *
	 * @since    0.4.0
	 */
	public $post_type;

	/**
	 * The offset setting
	 *
	 * @since    0.4.0
	 */
	protected $offset;

	/**
	 * The limit setting
	 *
	 * @since    0.4.0
	 */
	protected $limit;

	/**
	 * The query values
	 *
	 * @since    0.4.0
	 */
	public $values = array();

	public function __construct($search_string, $paged) {
		$this->search_string = trim($search_string);
    	$this->offset = intval($paged);

		$this->set_search_terms();
		$this->set_order();
		$this->set_orderby();
		$this->set_post_status();
		$this->set_post_type();
		$this->set_offset();
		$this->set_limit();

      	if( $this->post_type != NULL && $this->post_status != NULL && $this->orderby != NULL && $this->order != NULL ) {
			$this->query = $this->generate_query();
		
		} else {
			$this->query = false;
		}

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
	 * Set the search terms
	 *
	 * @since    0.4.0
	 */
	private function set_search_terms() {
		$search_term = array();

		$search_string = $this->set_to_lower_case($this->search_string);
		$search_string = $this->clean_whitespaces($search_string);
		$search_terms = explode(" ", $search_string);

	  if( is_array($search_terms) ){

			foreach ( $search_terms as $k => $v ) { 
				$search_term[] = '%'.$v.'%';
			}

	    	$this->search_terms = $search_term;
			return true;
	     
	  } else {
			return false;
	  }
	}

	/**
	 * Set the order setting
	 *
	 * @since    0.4.0
	 */
	private function set_order() {
	    $options = get_option('unasearch_settings');

	    if( is_array($options) && array_key_exists('order', $options) && ( trim($options['order']) == 'DESC' || trim($options['order']) == 'ASC') ) {
			$this->order = trim($options['order']);
			return true;
	      
	    } else {
			return false;
	    }
	}

	/**
	 * Set the orderby setting
	 *
	 * @since    0.4.0
	 */
	private function set_orderby() {
		$values = array('ID', 'post_author', 'post_title', 'post_name', 'post_type', 'post_date', 'post_modified', 'post_parent', 'comment_count', 'menu_order');
	    $options = get_option('unasearch_settings');

	    if( is_array($options) && array_key_exists('orderby', $options) && in_array(trim($options['orderby']), $values)){
			$this->orderby = trim($options['orderby']);
			return true;

	    } else {
			return false;
	    }
	}

	/**
	 * Set the post_status setting
	 *
	 * @since    0.4.0
	 */
	private function set_post_status() {
		$post_status = array();

	    $options = get_option('unasearch_settings');

	    if( is_array($options) && array_key_exists('post_status', $options) && is_array($options['post_status']) ){

			foreach ( $options['post_status'] as $k => $v ) { 
				$post_status[] = $v;
			}

			$this->post_status = $post_status;
	    	return true;
	    
	    } else {
	    	return false;
	    }
	}

	/**
	 * Set the post_status setting
	 *
	 * @since    0.4.0
	 */
	private function set_post_type() {
		$post_type = array();

	    $options = get_option('unasearch_settings');

	    if( is_array($options) && array_key_exists('post_types', $options) && is_array($options['post_types']) ){

			foreach ( $options['post_types'] as $k => $v ) { 
				$post_type[] = $v;
			}

			$this->post_type = $post_type;
	    	return true;
	    
	    } else {
	    	return false;
	    }
	}

	/**
	 * Set the offset setting
	 *
	 * @since    0.4.0
	 */
	private function set_offset() {
	    $options = get_option('unasearch_settings');

	    if( is_array($options) && array_key_exists('offset', $options) && (trim($options['offset']) != '' ) ){
			$this->offset = intval($options['offset']);
			return true;

	    } else {
			return false;
	    }
	}

	/**
	 * Set the limit setting
	 *
	 * @since    0.4.0
	 */
	private function set_limit() {
	    $options = get_option('unasearch_settings');

	    if( is_array($options) && array_key_exists('limit', $options) && (trim($options['limit']) != '' ) ){
			$this->limit = intval($options['limit']);
			return true;

	    } else {
			return false;
	    }
	}

	/**
	 * Generate the query
	 *
	 * @since    0.4.0
	 */
	private function generate_query() {
		global $wpdb;
		
		$query  = $this->build_select();
		$query .= $this->space();
		$query .= $this->build_from();
		$query .= $this->space();
		$query .= $this->build_join();
		$query .= $this->space();
		$query .= $this->build_where();
		$query .= $this->space();
		$query .= $this->build_orderby();
		
		/* @TODO : Create pagination
		$query .= $this->space();
		$query .= $this->build_limit();
		*/
		
		return $query;
	}

	/**
	 * Get a placeholder string from a array
	 *
	 * @since    0.4.0
	 */
	private function get_placeholders($array) {
		$placeholders = array();

		if( is_array($array) ) {
		    foreach( $array as $k => $v ) {
		      $placeholders[] = "'%s'";
		    }

		    $string = implode(', ', $placeholders);
			
			return $string;
		
		} else {
			return false;
		}
	}

	/**
	 * Generate the select clause
	 *
	 * @since    0.4.0
	 */
	private function build_select() {
		global $wpdb;

		$string = "SELECT DISTINCT p.ID";

		return $string;
	}

	/**
	 * Generate a whitespace
	 *
	 * @since    0.4.0
	 */
	private function space() {
		global $wpdb;

		$string = " ";

		return $string;
	}

	/**
	 * Generate the from clause
	 *
	 * @since    0.4.0
	 */
	private function build_from() {
		global $wpdb;

		$string = "FROM $wpdb->posts AS p";

		return $string;
	}

	/**
	 * Generate the inner join
	 *
	 * @since    0.4.0
	 */
	private function build_join() {
		global $wpdb;
		
		$string = "INNER JOIN {$wpdb->prefix}una_index AS i 
		ON i.post_id = p.ID 
		INNER JOIN {$wpdb->prefix}una_terms AS t 
		ON t.id = i.term_id";

		return $string;
	}
	
	/**
	 * Generate the where clause
	 *
	 * @since    0.4.0
	 */
	private function build_where() {
		global $wpdb;

		$post_status = $this->get_placeholders($this->post_status);
		$this->values = array_merge($this->values, $this->post_status);
		
		$post_type = $this->get_placeholders($this->post_type);
		$this->values = array_merge($this->values, $this->post_type);

		$this->values = array_merge($this->values, $this->search_terms);

		$string = "WHERE p.post_status IN ($post_status) 
		AND p.post_type IN ($post_type)
		AND p.post_date < NOW()";

		if( is_array($this->search_terms) && count($this->search_terms > 0) ) {
			$string .= " AND";

			foreach ( $this->search_terms as $k => $v ) {
				
				if( $k != 0 ) {
					$string .= " OR";
				}
				
				$string .= " t.term LIKE '%s'";
			}
			
		}
		return $string;
	}

	/**
	 * Generate the orderby part
	 *
	 * @since    0.4.0
	 */
	private function build_orderby() {
		global $wpdb;

		if( $this->orderby != NULL ) {
			$string = "ORDER BY p.$this->orderby";
		}

		if( $this->order != NULL ) {
			$string .= " $this->order";
		}

		return $string;
	}

	/**
	 * Generate the limit part
	 *
	 * @since    0.4.0
	 */
	private function build_limit() {
		global $wpdb;
		$this->values[] = $this->offset;
		$this->values[] = $this->limit;

		$string = "LIMIT '%d', '%d'";

		return $string;
	}
}