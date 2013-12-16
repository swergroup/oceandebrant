<?php

require_once( 'inc/core.class.php' );

class UploadPlus_Unit_Tests extends WP_UnitTestCase {

	function test_true(){
		$this->assertTrue( true );
	}

	function setup(){
		parent::setUp();
		$this->plugin = $GLOBALS['swer-uploadplus'];
	}

	function test_init(){
		$this->assertFalse( null == $this->plugin );
	}

	function test_version(){
		$this->assertEquals( '3.3.1', $this->plugin->version, 'Option: uploadplus_version does not match.' );
	}

	/*
	function test_extensions(){
	$this->assertEquals( 'jpeg', $this->plugin->find_extension( 'filename.jpeg' ), 'Extension #1 is not jpeg' );
	$this->assertEquals( 'gif', $this->plugin->find_extension( 'some boring file name.gif' ), 'Extension #2 is not gif' );
	$this->assertEquals( 'pdf', $this->plugin->find_extension( 'Upload+ WordPress plugin || SWERgroup (20120425).png.pdf' ), 'Extension #3 is not PDF' );
	}
	*/

	function test_transliteration(){
		$convert = $this->plugin->_utf8_transliteration( 'Αισθάνομαι τυχερός' );
		$this->assertEquals( 'esthanome ticheros', $convert, 'Greek string is not converted' );

		$convert2 = $this->plugin->_utf8_transliteration( 'زيدان أجمل اللقطات في دقيقتين' );
		$this->assertEquals( 'Zydan Ajml Al-Lqtat Fy Dqyqtyn', $convert2, 'Arabic string is not converted' );
	}

	function test_post_title_filter(){
	}

	function test_random(){
		update_option( 'uploadplus_random', 'on' );
		$filename = 'some boring, long and stupid file name.gif';
		$res = $this->plugin->upp_mangle_filename( $filename );
		$this->assertTrue( $res !== $filename );

		$name = str_replace( '.gif', '', $res );
		$this->assertTrue( strlen( $name ) == 20, 'Random file name should be 20 characters long + extension.' );
	}

	function test_prefix(){
		$filename = 'testfilename.gif';
		
		$test1 = $this->plugin->_add_prefix( $filename, '1', '' );
		$this->assertEquals( date( 'd' ).'-testfilename.gif', $test1, 'Prefix #1 not equal' );

		$test2 = $this->plugin->_add_prefix( $filename, '2', '' );
		$this->assertEquals( date( 'md' ).'-testfilename.gif', $test2, 'Prefix #3 not equal' );

		$test3 = $this->plugin->_add_prefix( $filename, '3', '' );
		$this->assertEquals( date( 'ymd' ).'-testfilename.gif', $test3, 'Prefix #3 not equal' );

		$test4 = $this->plugin->_add_prefix( $filename, '4', '' );
		$this->assertEquals( date( 'Ymd' ).'-testfilename.gif', $test4, 'Prefix #4 not equal' );

#		$test5 = $this->plugin->_add_prefix( $filename, '5', '' );
#		$this->assertEquals( date( 'YmdHi' ).'-testfilename.gif', $test5, 'Prefix #5 not equal' );

#		$test6 = $this->plugin->_add_prefix( $filename, '6', '' );
#		$this->assertEquals( date( 'YmdHis' ).'-testfilename.gif', $test6 ,'Prefix #6 not equal' );

		$test7 = $this->plugin->_add_prefix( $filename, '7', '' );
		$this->assertEquals( date( 'U' ).'-testfilename.gif', $test7, 'Prefix #7 not equal' );

		/* cases 8 and 9 are random, so we can't test them */

		$test10 = $this->plugin->_add_prefix( $filename, '10', '' );
		$this->assertEquals( 'Test-Blog'.'-testfilename.gif', $test10, 'Prefix #10 not equal' );

		$test11 = $this->plugin->_add_prefix( $filename, 'A', '' );
		$this->assertEquals( 'TestBlog'.'-testfilename.gif', $test11, 'Prefix #A not equal' );

		$test12 = $this->plugin->_add_prefix( $filename, 'B', '' );
		$this->assertEquals( 'testfilename.gif', $test12, 'Prefix #B not equal' );

		$test13 = $this->plugin->_add_prefix( $filename, null, 'custom_' );
		$this->assertEquals( 'custom_testfilename.gif', $test13, 'Prefix custom not equal' );

		$test14 = $this->plugin->_add_prefix( $filename, '1', 'custom_' );
		$this->assertEquals( 'custom_'.date( 'd' ).'-testfilename.gif', $test14, 'Prefix custom not equal' );
	}

}
