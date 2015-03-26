<?php

/**
 * The settings of the plugin
 *
 * @link       http://unacode.com/unasearch
 * @since      0.1.0
 *
 * @package    Unasearch
 * @subpackage Unasearch/admin
 */

class Unasearch_Settings {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 */
	public function __construct( $plugin_name ) {

		$this->plugin_name = $plugin_name;

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(
			__( 'Unasearch', $this->plugin_name ),
			__( 'Unasearch', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
			);

		add_submenu_page(
			$this->plugin_name,
			__( 'General settings', $this->plugin_name ),
			__( 'General settings', $this->plugin_name ),
			'manage_options',
			'display_unasearch_admin_page_settings',
			array( $this, 'display_plugin_admin_page' )
			);

		remove_submenu_page( $this->plugin_name, $this->plugin_name );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {

		$tabs = array(
			'general_settings' => __( 'General settings', $this->plugin_name )
		);

		$default_tab = 'general_settings';

		$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET[ 'tab' ] : $default_tab;

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/' . $this->plugin_name . '-admin-'.$active_tab.'.php';

	}

	/**
	 * Register settings
	 *
	 * @since    0.1.0
	 */
	function add_settings_admin() {


	  add_settings_section(
	    'index_settings_section',
	    __( 'Indexation', $this->plugin_name ),
	    array( $this, 'settings_general_index_callback'),
	    'display_unasearch_admin_page_settings'
	  );
	  
	  add_settings_field( 
	    'unasearch_settings_index',
	    __( 'Index terms', $this->plugin_name ),
	    array( $this, 'settings_index_callback'),
	    'display_unasearch_admin_page_settings',
	    'index_settings_section'
	  );

	  add_settings_section(
	    'general_settings_section',
	    __( 'General settings', $this->plugin_name ),
	    array( $this, 'settings_general_options_callback'),
	    'display_unasearch_admin_page_settings'
	  );
	  
	  add_settings_field( 
	    'unasearch_settings_post_types',
	    __( 'Post type(s)', $this->plugin_name ),
	    array( $this, 'settings_post_types_callback'),
	    'display_unasearch_admin_page_settings',
	    'general_settings_section'
	  );

	  add_settings_field( 
	    'unasearch_settings_orderby',
	    __( 'Order by', $this->plugin_name ),
	    array( $this, 'settings_orderby_callback'),
	    'display_unasearch_admin_page_settings',
	    'general_settings_section'
	  );

	  add_settings_field( 
	    'unasearch_settings_order',
	    __( 'Order', $this->plugin_name ),
	    array( $this, 'settings_order_callback'),
	    'display_unasearch_admin_page_settings',
	    'general_settings_section'
	  );

	  add_settings_field( 
	    'unasearch_settings_post_status',
	    __( 'Post status', $this->plugin_name ),
	    array( $this, 'settings_post_status_callback'),
	    'display_unasearch_admin_page_settings',
	    'general_settings_section'
	  );

	  
	}

	/**
	 * Callback for index button
	 *
	 * @since    0.1.0
	 */
	function settings_index_callback() {
		$html = '';
		$html .= '<input type="submit" name="index_terms" id="index_terms" class="button button-primary" value="'.__('Index terms', $this->plugin_name).'">';

    echo $html;
	}

	/**
	 * Callback for post type checkboxes
	 *
	 * @since    0.1.0
	 */
	function settings_post_types_callback() {
		$options = get_option('unasearch_settings');
    	$html       = '';		
		$post_types = get_post_types( '', 'objects' ); 

		if( is_array($post_types) && count($post_types) > 0 ){
	    foreach ( $post_types as $k => $v ) {	
	    
	    	if( ($v->name != 'revision') && ($v->name != 'nav_menu_item') && ($v->name != 'attachment') ){
	    		if( is_array($options) && array_key_exists('post_types', $options) && array_key_exists($v->name, $options['post_types']) ){
	    			$checked = 'checked="checked"';
	    		} else {
	    			$checked = '';
	    		}
	      	$html .= '<input '.$checked.' type="checkbox" id="'.$this->plugin_name.'_'.$v->name.'" name="'.$this->plugin_name.'_settings[post_types]['.$v->name.']" value="'.$v->name.'" />';
	      	$html .= '<label for="'.$this->plugin_name.'_'.$v->name.'">'.$v->label.'</label><br/>';
	    
	      }
	    
	    }
	  }

    echo $html;
	}

	/**
	 * Callback for orderby
	 *
	 * @since    0.1.0
	 */
	function settings_orderby_callback() {
		$options = get_option('unasearch_settings');
    	$html       = '';		
		$orderby = array(
			'ID'            => __( 'By post id', $this->plugin_name ),
			'post_author'   => __( 'By author', $this->plugin_name ),
			'post_title'    => __( 'By title', $this->plugin_name ),
			'post_name'     => __( 'By post name (post slug)', $this->plugin_name ),
			'post_type'     => __( 'By post type', $this->plugin_name ),
			'post_date'     => __( 'By date', $this->plugin_name ),
			'post_modified' => __( 'By last modified date', $this->plugin_name ),
			'post_parent'   => __( 'By parent id', $this->plugin_name ),
			'comment_count' => __( 'By number of comments', $this->plugin_name ),
			'menu_order'    => __( 'By menu order', $this->plugin_name ),
		); 

		if( is_array($orderby) && count($orderby) > 0 ){
			$html .= '<select id="'.$this->plugin_name.'_orderby" name="'.$this->plugin_name.'_settings[orderby]">';

		    foreach ( $orderby as $k => $v ) {	
		    
		    	if( is_array($options) && array_key_exists('orderby', $options) && ($k == $options['orderby']) ){
		    		$selected = 'selected="selected"';
		    	} else {
		    		$selected = '';
		    	}
		      	$html .= '<option '.$selected.' value="'.$k.'" />'.$v.'</option>';			    
		    }

		    $html .= '</select>';
		}

    echo $html;
	}

	/**
	 * Callback for order
	 *
	 * @since    0.1.0
	 */
	function settings_order_callback() {
		$options = get_option('unasearch_settings');
    	$html       = '';		
		$order = array(
			'ASC'  => __( 'Ascending order (from lowest to highest values)', $this->plugin_name ),
			'DESC' => __( 'Descending order (from highest to lowest values)', $this->plugin_name )
		); 

		if( is_array($order) && count($order) > 0 ){
			$html .= '<select id="'.$this->plugin_name.'_order" name="'.$this->plugin_name.'_settings[order]">';

		    foreach ( $order as $k => $v ) {	
		    
		    	if( is_array($options) && array_key_exists('order', $options) && ($k == $options['order']) ){
		    		$selected = 'selected="selected"';
		    	} else {
		    		$selected = '';
		    	}
		      	$html .= '<option '.$selected.' value="'.$k.'" />'.$v.'</option>';			    
		    }

		    $html .= '</select>';
		}

    echo $html;
	}

	/**
	 * Callback for post status checkboxes
	 *
	 * @since    0.1.0
	 */
	function settings_post_status_callback() {
		$options = get_option('unasearch_settings');
    $html       = '';		

		$post_status = array(
			'publish'  => __( 'Publish', $this->plugin_name ),
			'pending'  => __( 'Pending review', $this->plugin_name ),
			'draft'  => __( 'Draft', $this->plugin_name ),
			'auto-draft'  => __( 'Auto-draft', $this->plugin_name ),
			'future'  => __( 'Future', $this->plugin_name ),
			'private'  => __( 'Private', $this->plugin_name ),
			'inherit'  => __( 'Revision', $this->plugin_name ),
			'trash'  => __( 'Trash', $this->plugin_name )
		); 

		if( is_array($post_status) && count($post_status) > 0 ){

	    foreach ( $post_status as $k => $v ) {	

	    	if( is_array($options) && array_key_exists('post_status', $options) && array_key_exists($k, $options['post_status']) ){

	    		$checked = 'checked="checked"';
	    	} else {
	    		$checked = '';
	    	}
	      $html .= '<input '.$checked.' type="checkbox" id="'.$this->plugin_name.'_'.$k.'" name="'.$this->plugin_name.'_settings[post_status]['.$k.']" value="'.$k.'" />';
	      $html .= '<label for="'.$this->plugin_name.'_'.$k.'">'.$v.'</label><br/>';
	    
	    }
	  }

    echo $html;
	}

	/**
	 * Callback for display index option description
	 *
	 * @since    0.1.0
	 */
	function settings_general_index_callback() {
		//
	}

	/**
	 * Callback for display general option description
	 *
	 * @since    0.1.0
	 */
	function settings_general_options_callback() {
		//
	}

	/**
	 * Register settings in options database
	 *
	 * @since    0.1.0
	 */
	function register_settings() {

	  register_setting(
	    'display_unasearch_admin_page_settings',
	    'unasearch_settings'
	  );

	}

}