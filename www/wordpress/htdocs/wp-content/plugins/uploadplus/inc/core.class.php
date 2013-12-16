<?php
#if( ! array_key_exists( 'swer-uploadplus-core', $GLOBALS ) ) { 

/**
 * UploadPlus core class
 * @package WordPress_Plugins
 * @subpackage UploadPlus
 */
class SWER_uploadplus_core {

	/**
	* default separator
	*/
	var $sep = '-';

	var $exif_mime = array( 'image/jpeg', 'image/tiff' );

	/**
	* transliterate greek script into english characters
	* @link http://www.freestuff.gr/forums/viewtopic.php?p=194579#194579
	* @param $text    (maybe-)greek string to transliterate
	* @return string  transliterated string 
	*/
	static function sanitize_greeklish( $text ) {
		$expressions = array(
			'/[αΑ][ιίΙΊ]/u' => 'e',
			'/[οΟΕε][ιίΙΊ]/u' => 'i',
			'/[αΑ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'af$1',
			'/[αΑ][υύΥΎ]/u' => 'av',
			'/[εΕ][υύΥΎ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'ef$1',
			'/[εΕ][υύΥΎ]/u' => 'ev',
			'/[οΟ][υύΥΎ]/u' => 'ou',

			'/(^|\s)[μΜ][πΠ]/u' => '$1b',
			'/[μΜ][πΠ](\s|$)/u' => 'b$1',
			'/[μΜ][πΠ]/u' => 'mp',
			'/[νΝ][τΤ]/u' => 'nt',
			'/[τΤ][σΣ]/u' => 'ts',
			'/[τΤ][ζΖ]/u' => 'tz',
			'/[γΓ][γΓ]/u' => 'ng',
			'/[γΓ][κΚ]/u' => 'gk',
			'/[ηΗ][υΥ]([θΘκΚξΞπΠσςΣτTφΡχΧψΨ]|\s|$)/u' => 'if$1',
			'/[ηΗ][υΥ]/u' => 'iu',

			'/[θΘ]/u' => 'th',
			'/[χΧ]/u' => 'ch',
			'/[ψΨ]/u' => 'ps',
	
			'/[αάΑΆ]/u' => 'a',
			'/[βΒ]/u' => 'v',
			'/[γΓ]/u' => 'g',
			'/[δΔ]/u' => 'd',
			'/[εέΕΈ]/u' => 'e',
			'/[ζΖ]/u' => 'z',
			'/[ηήΗΉ]/u' => 'i',
			'/[ιίϊΙΊΪ]/u' => 'i',
			'/[κΚ]/u' => 'k',
			'/[λΛ]/u' => 'l',
			'/[μΜ]/u' => 'm',
			'/[νΝ]/u' => 'n',
			'/[ξΞ]/u' => 'x',
			'/[οόΟΌ]/u' => 'o',
			'/[πΠ]/u' => 'p',
			'/[ρΡ]/u' => 'r',
			'/[σςΣ]/u' => 's',
			'/[τΤ]/u' => 't',
			'/[υύϋΥΎΫ]/u' => 'i',
			'/[φΦ]/iu' => 'f',
			'/[ωώ]/iu' => 'o',
		);
		$text = preg_replace( array_keys( $expressions ), array_values( $expressions ), $text );
		return $text;
	}

	/*
	* find file extension (legacy code)
	* @deprecated
	static function find_extension( $filename ) { 
		$check = wp_check_filetype( $filename );
		return $check['ext'];
	} 
	*/

	/*
	* find file name without extension (legacy code)
	* @deprecated
	function find_filename( $filename ) { 
		$explode = explode( '/', $filename );
		$explode = array_reverse( $explode );
		return $explode[0];
	} 
	*/

	/**
	* wrapper around (future) multiple transliteration logics
	* @param string $file_name  filename to transliterate
	* @return string  transliterated filename 
	*/
	static function _utf8_transliteration( $file_name ){
		if ( ! class_exists( 'I18N_Arabic_Transliteration' ) ) 
			$arabic = new I18N_Arabic( 'Transliteration' );

		$file_name = trim( I18N_Arabic_Transliteration::ar2en( $file_name ) );
		$file_name = self::sanitize_greeklish( $file_name );
		$file_name = URLify::downcode( $file_name ); 
		return $file_name;
	}

	/*
	function _clean_filename( $ext, $file_name ){
	$file_name = str_replace( '.'.$ext, '', $file_name );
	$file_name = str_replace( '.', '', $file_name );
	$file_name = preg_replace( '~[^\\pL0-9_]+~u', '-', $file_name );
	$file_name = preg_replace( '/^\s+|\s+$/', '', $file_name );
	$file_name = $file_name . '.' . $ext;
	return $file_name;
	} 
	*/

	/**
	* convert string between cases, according to options
	* @param string   $file_name file name to convert
	* @return string  converted filename 
	*/
	static function _clean_case( $file_name ){
		$case = get_option( 'uploadplus_case' );
		switch ( $case[0] ):
		case '1' :
			$file_name = strtolower( $file_name );
			break;
		case '2' :
			$file_name = strtoupper( $file_name );
			break;
		case '3' :
			$file_name = ucwords( $file_name );
			break;
		default:
			$file_name = trim( $file_name );
			break;
		endswitch;
		return $file_name;
	}

	/**
	* (deprecated) replace whitespaces with dashes or underscores
	* @deprecated
	* @param string   $file_name file name to convert
	* @return string  converted filename 
	*/
	static function _clean_global( $file_name ){
		global $sep;
		$cleanlevel = get_option( 'uploadplus_separator' );
		$level = isset( $cleanlevel[0] ) ? $cleanlevel[0] : 'dash';

		switch ( $level ):
		case 'dash' :
		default:
			$file_name = preg_replace( '/[-\s]+/', '-', $file_name );
			$sep = '-';
			break;
		case 'space' :
			$file_name = preg_replace( '/[-\s]+/', ' ', $file_name );
			$sep = ' ';
			break;
		case 'underscore':
			$file_name = preg_replace( '/[-\s]+/', '_', $file_name );
			$sep = '_';
			break;
		endswitch;
		return $file_name;
	}

	/**
	* add a standard or custom prefix to a filename
	* @param string $file_name  filename to add prefix to 
	* @param string $options    if not null, force a standard prefix (1-10 || A-B)
	* @param string $custom     if not null, force a custom prefix
	* @return string 
	*/
	static function _add_prefix( $file_name, $options = '', $custom = '' ){
		global $current_user;
		$option_sep = get_option( 'uploadplus_separator', true );
		switch ( $option_sep ):
		case 'dash' :
		default:
			$sep = '-';
			break;
		case 'space' :
			$sep = ' ';
			break;
		case 'underscore':
			$sep = '_';
			break;
		endswitch;
		
		$options = ( $options == '' ) ? get_option( 'uploadplus_prefix' ) : $options;
		$custom  = ( $custom == '' ) ? get_option( 'uploadplus_customprefix' ) : $custom;

		switch ( $options ):
		case '1':	
			$file_name = date( 'd' ) . $sep . $file_name;
			break;
		case '2':
			$file_name = date( 'md' ) . $sep . $file_name;
			break;
		case '3':
			$file_name = date( 'ymd' ) . $sep . $file_name;
			break;
		case '4':
			$file_name = date( 'Ymd' ) . $sep . $file_name;
			break;
		# case '5':
		#	$file_name = date( 'YmdHi' ).$sep . $file_name;
		#	break;
		# case '6':
		#	$file_name = date( 'YmdHis' ).$sep . $file_name;
		#	break;
		case '7':
			$file_name = date( 'U' ) . $sep . $file_name;
			break;
		case '8':
			$file_name = mt_rand() . $sep . $file_name;
			break;
		case '9':
			$file_name = md5( mt_rand() ) . $sep . $file_name;
			break;
		case '10':
			$file_name = str_replace( array( '.', '_', '-', ' ' ) ,$sep,  get_bloginfo( 'name' ) ) . $sep . $file_name;
			break;
		case 'A':
			$file_name = str_replace( array( '.', '_', '-', ' ' ) ,'', get_bloginfo( 'name' ) ) . $sep . $file_name;
			break;
		case 'B':
			$uploads   = wp_upload_dir();
			$dir = ( $uploads['path'] );
			$filename  = wp_unique_filename( $dir, $file_name, null );
			$file_name = $filename;
			break;
		case 'C':
			get_currentuserinfo();
			$file_name = $current_user->user_login . $sep . $file_name;
			break;
		default: 
			$file_name = $file_name; 
			break;
		endswitch;

		if ( $custom !== '' ):
			$return_file_name = $custom . $file_name;
		else :
			$return_file_name = $file_name;
		endif;
		return $return_file_name;
	}

	/**
	* (dev) hash filename and add it to metadata
	* @param string  $file_name file name to hash
	* @return string  filename hash 
	*/
	function _hash_filename( $file_name ) {
		$info = pathinfo( $file_name );
		$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
		$name = basename( $file_name, $ext );
		// add to post_meta
		return md5( $name ) . $ext;
	}
	# add_filter('sanitize_file_name', 'make_filename_hash', 10);

	/**
	* master function: applies every plugin step
	* @param string  $file_name file name to clean
	* @return string  clean filename 
	*/
	static function upp_mangle_filename( $file_name ){	
		# $ext  = self::find_extension( $file_name );
		$check = wp_check_filetype( $file_name );
		$ext   = $check['ext'];
		$utf8  = get_option( 'uploadplus_utf8toascii' );
		if ( $utf8[0] == '1' ):
			$file_name = self::_utf8_transliteration( $file_name );
		endif;

		$random = get_option( 'uploadplus_random' );
		if ( 'on' === $random ):
			$file_name = substr( sha1( time() ), 0, 20 ) . '.' . $ext;
		else :
			$file_name = self::_add_prefix( $file_name );
			$file_name = sanitize_file_name( $file_name );
			$file_name = self::_clean_case( $file_name );
			$file_name = self::_clean_global( $file_name );
		endif;
		return $file_name;
	}

	/**
	* Apply cleaning methods
	* 
	* 
	* @param array $meta upload meta
	* @return array
	*/
	function wp_handle_upload_prefilter( $meta ){
		// clean filename
		$meta['name'] = self::upp_mangle_filename( $meta['name'] );		

		// check for EXIF malware
		if ( in_array( $meta['type'], $this->exif_mime ) ):
			$exif_data = exif_read_data( $meta['tmp_name'], 0, true );

			// http://blog.sucuri.net/?p=7654
			if ( isset( $exif_data['IFD0']['Model'] ) )
				preg_match( '/\b(base64_decode|eval)\b/i', $exif_data['IFD0']['Model'], $matches );

			if (
				isset( $exif_data['IFD0']['Make'] ) &&
				'/.*/e' === $exif_data['IFD0']['Make'] &&
				0 < count( $matches ) 
				):
					// Set error to 'reject from extension'. 
					// Looks like the appropriate level here.
					$meta['error'] = 8;

					error_log( 'EXIF malware detected! Check http://blog.sucuri.net/?p=7654 for details. ' . "\n" . json_encode( array( $meta, $exif_data ) ) );

					if ( isset( $_POST['html-upload'] ) )
						wp_die( 'EXIF malware detected! Check http://blog.sucuri.net/?p=7654 for details.', 'EXIF malware detected' );
			endif;
		endif;

		return $meta;
	}

	/**
	* read image metadata
	* @param array $meta  file meta
	* @param string $file filename
	* @param string $sourceImageType
	* @return array
	*/
	function wp_read_image_metadata( $meta, $file ){
		$check = wp_check_filetype( $file );
		$ext = $check['ext'];
		$cur = str_replace( '.'.$ext, '', $file );
		$meta['caption'] = str_replace( array( $ext, '_', '-' ), ' ', $cur );
		return $meta;
	}

	/*
	* @deprecated
	function sanitize_file_name( $filename, $filename_raw = null ){
		return $filename;
	}
	*/


	/*
	function wp_handle_upload( $array ){             
		global $action;
		$current_name = self::find_filename( $array['file'] );
		$new_name = self::upp_mangle_filename( $current_name );		

		$lpath = str_replace( $current_name, '', urldecode( $array['file'] ) );
		$wpath = str_replace( $current_name, '', urldecode( $array['url'] ) );
		$lpath_new = $lpath . $new_name;
		$wpath_new = $wpath . $new_name;
		if ( @rename( $array['file'], $lpath_new ) )
			return array(
			 'file' => $lpath_new,
			 'url' => $wpath_new,
			 'type' => $array['type'],
			 );
		return $array;
	}
	*/

	function add_attachment( $post_ID ){
		if ( false == get_post( $post_ID ) )
			return false;

		$obj   = get_post( $post_ID );
		$title = $obj->post_title;
		$title = apply_filters( 'uploadplus_add_attachment_title', $title );

		// Update the post into the database
		$uploaded_post = array();
		$uploaded_post['ID'] = $post_ID;
		$uploaded_post['post_title'] = $title;
		wp_update_post( $uploaded_post );

		update_post_meta( $post_ID, 'filehash', $this->_hash_filename( $title ) );

		return $post_ID;
	}

}

#    $GLOBALS['swer-uploadplus-core'] = new SWER_uploadplus_core();
#}
