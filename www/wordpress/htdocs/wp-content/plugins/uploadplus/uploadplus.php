<?php
/*
Plugin Name: UploadPlus : File Name Cleaner
Plugin URI: http://wordpress.org/extend/plugins/uploadplus
Git URI: https://github.com/swergroup/uploadplus
Description: Clean file names and enhance security while uploading. 
Author: SWERgroup
Version: 3.3.1
Author URI: http://swergroup.com/

Copyright (C) 2007+ Paolo Tresso / SWERgroup (http://swergroup.com/)

Included code libraries:
*   Arabic PHP (LGPL) - http://www.ar-php.org/
*   URLify (Django PHP port) - https://github.com/jbroadway/urlify/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define( 'UPLOADPLUS_VERSION', '3.3.1' );
define( 'UPLOADPLUS_PATH', plugin_dir_path( __FILE__ ) );

require_once UPLOADPLUS_PATH . '/lib/URLify.php';
require_once UPLOADPLUS_PATH . '/lib/Arabic.php';
require_once UPLOADPLUS_PATH . '/inc/core.class.php';
require_once UPLOADPLUS_PATH . '/inc/admin.class.php';
if ( defined( 'WP_CLI' ) && WP_CLI ) {
		include UPLOADPLUS_PATH . '/inc/wp-cli.class.php';
}

/**
 * on activation, check options or create them
 */
function uploadplus_activate(){
	global $wp_version;

	if ( version_compare( $wp_version, '3.3', '<' ) ) {
		deactivate_plugins( __FILE__ );
		wp_die( __( 'UploadPlus requires WordPress 3.3 or newer', 'uploadplus' ) );
	}

	if ( ! get_option( 'uploadplus_version' ) )
		update_option( 'uploadplus_version', UPLOADPLUS_VERSION );

	if ( ! get_option( 'uploadplus_separator' ) )
		update_option( 'uploadplus_separator' ,'dash' );

	if ( ! get_option( 'uploadplus_case' ) )
		update_option( 'uploadplus_case', '0' );

	if ( ! get_option( 'uploadplus_prefix' ) )
		update_option( 'uploadplus_prefix', '0' );

	if ( ! get_option( 'uploadplus_customprefix' ) )
		update_option( 'uploadplus_customprefix', '' );

	if ( ! get_option( 'uploadplus_utf8toascii' ) )
		update_option( 'uploadplus_utf8toascii', '0' );
}

/**
 * on deactivation, do nothing (this time)
 */
function uploadplus_deactivate(){
	return;
}


/**
 * on uninstall, delete options and say bye.
 */
function uploadplus_uninstall(){
	delete_option( 'uploadplus_separator' );
	delete_option( 'uploadplus_case' );
	delete_option( 'uploadplus_prefix' );
	delete_option( 'uploadplus_customprefix' );
	delete_option( 'uploadplus_utf8toascii' );
	delete_option( 'uploadplus_version' );
}


register_activation_hook( __FILE__, 'uploadplus_activate' );
register_deactivation_hook( __FILE__, 'uploadplus_deactivate' );
register_uninstall_hook( __FILE__, 'uploadplus_uninstall' );



if ( ! array_key_exists( 'swer-uploadplus', $GLOBALS ) ) { 
	class SWER_uploadplus extends SWER_uploadplus_core{

		var $version = UPLOADPLUS_VERSION;

		#var $error = '';

		/**
		 * actions and filter init
		 */
		function __construct() {
			#$core = new SWER_uploadplus_core();
			add_action( 'admin_init', array( &$this, '_admin_init' ) );
			add_action( 'add_attachment', array( &$this, 'add_attachment' ) );
			
			add_filter( 'wp_handle_upload_prefilter', array( &$this, 'wp_handle_upload_prefilter' ) );
			add_filter( 'wp_read_image_metadata', array( 'SWER_uploadplus_core', 'wp_read_image_metadata' ), 1, 2 );
			#add_filter( 'sanitize_file_name', array( &$this, 'sanitize_file_name' ) );
			#$this->error = new WP_Error();
		}

		/**
		 * admin initialization
		 */
		function _admin_init() {
			add_settings_section( 'upp_options_section', __( 'UploadPlus options', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_intro' ), 'media' );

			add_settings_field( 'uploadplus_separator', __( 'Separator', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_box_cleanlevel' ), 'media', 'upp_options_section' );
			register_setting( 'media', 'uploadplus_separator' );
			
			add_settings_field( 'uploadplus_case', __( 'Letter Case', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_box_case' ), 'media', 'upp_options_section' );
			register_setting( 'media', 'uploadplus_case' );

			add_settings_field( 'uploadplus_prefix', __( 'File Prefix', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_box_prefix' ), 'media', 'upp_options_section' );
			register_setting( 'media', 'uploadplus_prefix' );

			add_settings_field( 'uploadplus_customprefix', __( 'Custom Prefix', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_box_customprefix' ), 'media', 'upp_options_section' );
			register_setting( 'media', 'uploadplus_customprefix' );

			add_settings_field( 'uploadplus_utf8toascii', __( 'Transliteration', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_box_utf8toascii' ), 'media', 'upp_options_section' );
			register_setting( 'media', 'uploadplus_utf8toascii' );

			add_settings_field( 'uploadplus_random', __( 'Random File Name', 'uploadplus' ), array( 'SWER_uploadplus_admin', 'upp_options_box_random' ), 'media', 'upp_options_section' );
			register_setting( 'media', 'uploadplus_random' );

			/*
			add_settings_field('uploadplus_image_desctiption', 'Image Options', 
					array( 'SWER_uploadplus_admin', 'upp_options_box_image'), 'media', 'upp_options_section');
			register_setting('media', 'uploadplus_utf8toascii');
			*/
		}

		function test_extension( $string ){
			return self::find_extension( $string );
		}

		function test_filename( $string ){
			return self::find_filename( $string );
		}

	}   // end class

	$GLOBALS['swer-uploadplus'] = new SWER_uploadplus();
}
