=== UploadPlus : File Name Cleaner ===
Contributors: pixline, swergroup
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CX6VQ6FVJFN4L
Tags: uploads, images, image, custom, random, security, sanitization, transliteration, 
Requires at least: 3.3
Tested up to: 3.7.1
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Clean file names and enhance security while uploading.

== Description ==

*NEW: [EXIF header malware/backdoor](http://blog.sucuri.net/?p=7654) sanitization!*

Filenames on the web are different than those on the desktop. Empty spaces and strange characters doesn't belong to web space. Enter UploadPlus: you can set your rules and clean your files' name while they upload. Three basic rules:

* keep only alphanumeric [A-Za-z] and digits, spaces and strange characters stripped out;
* convert spaces and strange characters into dashes (-)
* convert spaces and strange characters in underscores (_)

You can apply website-based or date-based prefix, even a custom prefix. Lowercase or Uppercase? Your choice! 
Some default prefix:

* time/date based timestamps via PHP date()
* blog name/slug
* random
* and many more!

UploadPlus can also transliterate filenames (text conversion from a non-latin script to raw latin characters).
You can preview settings in the media settings page without uploading files. 


= Support = 

We'll do our best to support the plugin on [support forum](http://wordpress.org/support/plugin/page2cat). Paid support and custom coding options are available on the [SWERgroup HelpDesk](https://swergroup.zendesk.com).

If you found this plugin useful or need a new feature feel free to [sponsor a coding/support session](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CX6VQ6FVJFN4L)!

= Credits = 

GPL2&copy; 2008+ Paolo Tresso/[SWERgroup](http://swergroup.com/sviluppo/siti-internet-torino/).


== Screenshots ==

1. Option panel (under Options &raquo; Media)

== Changelog ==

= 3.3.1 =

(01/12/2013)

* NEW built-in EXIF malware check (http://blog.sucuri.net/?p=7654)
* NEW username prefix option
* NEW random filename option
* NEW Wider WP compatibility tests (3.3.x, 3.4.x, 3.5.x, 3.6)
* NEW Wider PHP compatibility tests (5.2, 5.3, 5.4, 5.5)

* DEV Codesniffer + PHPlint pre-commit routines
* DEV PHPdoc descriptions
* DEV Fix PHP warnings and strict
* DEV Fix WP coding styles

= 3.2.1 =

(24/02/2013)
* FIX   filename in media uploader
* FIX   non-latin strings transliteration
* NEW   enhanced arabic transliteration support

= 3.0.2 =

(24/11/12) 
* FIX default options on first activation
* FIX prefix bug

= Older Releases =

* 3.0.1 (04/11/12) custom prefix
* 3.0   (04/11/12) upgrade for WordPress 3.4.2, partial rewrite and refactoring.
* 2.7	  (28/02/09) version 2.7 for WordPress 2.7 working
* 2.7b1	(26/02/09) beta version for WordPress 2.7 only 
* 2.5.1 (10/03/08) little bugfix
* 2.5	  (02/03/08) tagged 2.5 release. better german support, props denis.
* 2.5b1 (26/02/08) preliminary WordPress 2.5 support, version bump to match WP version, utf8-based transliteration.
* 0.3.3 (14/09/07) works with WordPress 2.3 and WordPress MU 1.2.*
* 0.3.2 (12/07/07) silly typos fixed :-)
* 0.3.1 (11/07/07) dd_ prefix added by Ovidiu request
* 0.3   (21/06/07) optional prefix, preview of changes. first tagged stable!
* 0.2   (06/06/07) more options
* 0.1d  (20/03/07) better register hook and readme
* 0.1c  (06/03/07) fix on plugin activation, options
* 0.1a  (20/02/07) initial release


== Installation ==

1. Download the plugin Zip archive.
1. Upload `uploadplus` folder to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Tune the 'UploadPlus Plugin' section under 'Options'->'Media' according to your own rules.
1. Enjoy :-)
