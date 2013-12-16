<?php
/**
 * UploadPlus command-line utilities
 *
 * @package wp-cli
 */
class UploadPlus_Cmds extends WP_CLI_Command{

	function clean( $args ) {
		list( $string ) = $args;
		$file_name = SWER_uploadplus_core::_clean_global( $string );
		$file_name = SWER_uploadplus_core::_clean_case( $file_name );
		#$file_name = SWER_uploadplus_core::_add_prefix( $file_name );
		WP_CLI::line( $file_name );
	}

	function convert( $args ) {
		list( $string ) = $args;
		$file_name = SWER_uploadplus_core::_utf8_transliteration( $string );
		WP_CLI::line( $file_name );
	}

	function full( $args ) {
		list( $string ) = $args;
		$file_name = SWER_uploadplus_core::_clean_global( $string );
		$file_name = SWER_uploadplus_core::_clean_case( $file_name );
		$file_name = SWER_uploadplus_core::_utf8_transliteration( $string );
		WP_CLI::line( $file_name );
	}

}

WP_CLI::add_command( 'uploadplus', 'UploadPlus_Cmds' );
