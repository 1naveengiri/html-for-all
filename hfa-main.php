<?php
/**
 * Plugin Name: .html for all url
 * Description: Adds .html to post, pages, CPT in WordPress.
 * Author: buddydevelopers
 * Version: 1.2
 * Author URI: http://www.buddydevelopers.com/
 *
 * @package HFA\Fronted
 */

namespace BUDDY_SEO_URL;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Plugin related Vars.
define( 'HFA_PLUGIN_FILE',       __FILE__ );
define( 'HFA_PLUGIN_BASENAME',   plugin_basename( __FILE__ ) );

// Include addition files.
require_once( plugin_dir_path( __FILE__ ) . 'class-html-for-all.php' );
$instance = Html_For_All::get_instance();
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-hfa-setting.php' );
$instance = Html_For_All_AdminSettings::get_instance();
