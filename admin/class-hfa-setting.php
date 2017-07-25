<?php
/**
 * File to add setting for the plugin
 *
 * @package HFA\Setting
 */

namespace BUDDY_SEO_URL;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Html_For_All_AdminSettings to add admin page setting for plugin.
 */
class Html_For_All_AdminSettings {

	/**
	 *  Html_For_All_AdminSettings class instance
	 *
	 *  @var private static $instance
	 */
	private static $instance;

	/**
	 * __construct Html_For_All_AdminSettings class constructor
	 */
	function __construct() {
		add_filter( 'plugin_action_links_' . HFA_PLUGIN_BASENAME, array( $this, 'hfa_settings_page_link' ) );
		add_action( 'admin_menu', array( $this, 'hfa_settings_plugin_menu' ) );
	}

	/**
	 * Get class instance in singleton way.
	 *
	 * @return object Html_For_All_AdminSettings::$instance
	 */
	public static function get_instance() {
		if ( empty( SELF::$instance ) ) {
			$instance = new Html_For_All_AdminSettings();
		}
		return SELF::$instance;
	}

	/**
	 * Add a link to the plugin settings page.
	 *
	 * @since  1.0
	 *
	 * @param  array $links Plugin links.
	 *
	 * @return array  Links with the settings
	 */
	function hfa_settings_page_link( $links ) {

		$link    = $this->hfa_get_settings_page_url();
		$links[] = '<a href="' . $link . '">' . __( 'Settings', 'awesome-support' ) . '</a>';

		return $links;

	}

	/**
	 * Function to create setting page URL for plugin.
	 *
	 * @return String Setting page URL
	 */
	function hfa_get_settings_page_url() {
		$query_args = array( 'page' => 'hfa-settings' );
		$admin_settings_url  = admin_url( 'options-general.php' );
		return add_query_arg( $query_args, $admin_settings_url );
	}

	/**
	 * Function to add HFA setting page
	 */
	function hfa_settings_plugin_menu() {
		add_options_page( 'HFA Settings', '.html For All', 'manage_options', 'hfa-settings', array( $this, 'hfa_settings_callback' ) );
	}

	/**
	 * Html for all setting page.
	 *
	 * @return html of setting page
	 */
	function hfa_settings_callback() {
	}
}
