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
			'none'          => __( 'No order', $this->plugin_name ),
			'ID'            => __( 'By post id', $this->plugin_name ),
			'author'        => __( 'By author', $this->plugin_name ),
			'title'         => __( 'By title', $this->plugin_name ),
			'name'          => __( 'By post name (post slug)', $this->plugin_name ),
			'type'          => __( 'By post type', $this->plugin_name ),
			'date'          => __( 'By date', $this->plugin_name ),
			'modified'      => __( 'By last modified date', $this->plugin_name ),
			'parent'        => __( 'By parent id', $this->plugin_name ),
			'rand'          => __( 'Random order', $this->plugin_name ),
			'comment_count' => __( 'By number of comments', $this->plugin_name ),
			'menu_order'    => __( 'By menu order', $this->plugin_name )
		); 

		if( is_array($orderby) && count($orderby) > 0 ){
			$html .= '<select id="'.$this->plugin_name.'_orderby" name="'.$this->plugin_name.'_settings[orderby]">';

		    foreach ( $orderby as $k => $v ) {	
		    
		    	if( is_array($options) && array_key_exists('orderby', $options) && ($k == $options['orderby']) ){
		    		$selected = 'selected="selected"';
		    	} else {
		    		$selected = '';
		    	}
		      	$html .= '<option '.$selected.' type="checkbox" value="'.$k.'" />'.$v.'</option>';			    
		    }

		    $html .= '</select>';
		}

    echo $html;
	}

	/**
	 * Callback for display general option description
	 *
	 * @since    0.1.0
	 */
	function settings_general_options_callback() {

	  echo '<p>' . __( 'Check the post types to include in the search :', $this->plugin_name ) . '</p>';

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