<?php
/**
 * Plugin Name: WpOptionsAdmin
 * Plugin URI:  http://github.com/ginsterbusch/
 * Text Domain: wp-options-admin
 * Domain Path: /languages
 //* Description: Simple overview of ALL available options, stored in the wp_options table. Also allows you to add, edit and remove single options. Only meant for developers, ie. you should know what you're doing! The plugin name is a spoof on "PhpMyAdmin".
 * Author:      Fabian Wolf
 * Author URI:  http://usability-idealist.de/
 * Version:     0.1
 * License:     GPLv3
 */

add_action( 'plugins_loaded', array ( 'WpOptionsAdmin', 'get_instance' ) );

class WpOptionsAdmin {	
	var $pluginName = 'WpOptionsAdmin',
		$pluginPrefix = 'woa_',
		$pluginVersion = '0.1',
		$pluginMenuSlug = 'woa-admin';
	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;

	

	/**
	 * Access this plugin’s working instance
	 *
	 * @wp-hook plugins_loaded
	 * @since   04/05/2013
	 * @return  object of this class
	 */
	public static function get_instance() {

		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}

	
	function __construct() {
		// initialize variables
		$this->strTemplatePath = plugin_dir_path( __FILE__) . '/templates/';
		
		
		// add the menu
		add_action('admin_menu', array( $this, 'add_admin_menu' ) );
	}
	
	
	
	function add_admin_menu() {
		add_management_page(
			$this->pluginName, $this->pluginName, 'manage_options', $this->pluginMenuSlug, array( $this, 'admin_page' )
		);
	}
	
	function admin_page() {
		// initial variables
		$strTemplateName = 'home.php';
		$strAction = ( !empty($_GET['action'] ) ? $_GET['action'] : false );
		
		// controller
		switch( $strAction ) {
			
			case 'add':
				$strTemplateName = 'option-add.php';
				break;
			case 'edit':
				$option = get_option( $_GET['id'], false );
			
				if( !empty( $option ) ) {
			
					$strTemplateName = 'option-edit.php';
				} else {
					$msg = array(
						'type' => 'error',
						'message' => '',
					);
					$this->add_message( $msg );
				}
				
				break;
				
			case 'remove':
				break;
				
			case 'overview':
			default:
				$options = $this->fetch_options();
				break;
		}
		
		//new __debug( $options, 'compiled options' );
		
		// output data (aka view)
		include( $this->strTemplatePath . $strTemplateName );
	}
	
	function fetch_options() {
		global $wpdb;
		$return = false;
		$result = $wpdb->get_results( "SELECT * FROM $wpdb->options ORDER BY option_name" );

		//new __debug( $result, 'query results' );

		if( !empty( $result ) ) {
			$iOptionCount = 0;

			foreach ( (array) $result as $option ) {
				
				
				if( !empty( $option->option_name ) ) {
					$arrReturn[ $iOptionCount ] = new stdClass();
					$arrReturn[ $iOptionCount ]->label = esc_attr( $option->option_name );
					$arrReturn[ $iOptionCount ]->name = esc_html( $option->option_name );
					
					if ( is_serialized( $option->option_value ) ) {
						$arrReturn[ $iOptionCount ]->value = maybe_unserialize( $option->option_value );
					} else {
						$arrReturn[ $iOptionCount ]->value = $option->option_value ;
					}
					
					
					$iOptionCount++;
				}
			}
			
			if( isset( $arrReturn ) ) {
				$return = $arrReturn;
			}
			
		}
		
		return $return;
	}
	
	/**
	 * Simple message adding using the WP Admin UI
	 * 
	 * @param string $type 		Message type, eg. error, info, update, etc.
	 * @param string $message	The actual message. May contain HTML (which automatically DISABLES p-tag-wrapping)
	 */
	function add_message( $arrMessage = array() ) {
		$return = false;
		
		if( !empty( $arrMessage ) && !empty( $arrMessage['message'] ) ) {
			
			// compile message class
			$arrClass = array('fade');
			
			if( !empty( $arrMessage['type'] ) ) {
				$arrClass[] = $arrMessage['type'];
			}
			
			// compile message text
			$strMessage = $arrMessage['message'];
			$strUnformattedMessage = strip_tags( $strMessage, '<strong><em><b><i><a><button><img>' );
			
			// no html
			if( trim($strUnformattedMessage) == trim( $strMessage ) ) {
				$strMessage = '<p>'.$strMessage.'</p>';
			}
	
			$strMessageContainer = '<div class="' . implode(' ', $arrClass ) . '">'.$strMessage.'</div>';
	
			// set uo error message
			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				add_action( 'network_admin_notices', create_function( '', "echo '$strMessageContainer';" ) );
			}
			
			add_action( 'admin_notices', create_function( '', "echo '$strMessageContainer';" ) );
			
			$return = true;
		}
		return $return;
	}
	
	
	
}
