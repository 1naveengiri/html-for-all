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
		add_action( 'admin_enqueue_scripts', array( $this, 'hfa_add_setting_style' ) );
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
	 * Add style for setting form
	 */
	function hfa_add_setting_style() {
		wp_enqueue_style( 'hfa_setting_style', plugins_url( 'style.css', __FILE__ ) );
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
	 * HTML for all setting page.
	 */
	function hfa_settings_callback() {
		if ( isset( $_POST['hfa_save_post_types'] ) && ! empty( $_POST['hfa_save_post_types'] ) ) {
			$selected_post_type = array();
			if ( isset( $_POST['hfa_post_types'] )  && ! empty( $_POST['hfa_post_types'] ) ) {
				$selected_post_type = wp_unslash( $_POST['hfa_post_types'] );
			}
			global $wp_rewrite;
			if ( ! empty( $selected_post_type ) && in_array( 'post', $selected_post_type ) ) {
				$permalink_structure = get_option( 'permalink_structure' );
				if ( ! empty( $permalink_structure ) ) {
					if ( ! strpos( $permalink_structure , '.html' ) ) {
						update_option( 'old_permalink_structure', $permalink_structure );
					}
					$permalink_structure = explode( '/', $permalink_structure );
					$total_element = count( $permalink_structure );
					if ( isset( $permalink_structure[ $total_element - 1 ] ) && empty( $permalink_structure[ $total_element - 1 ] ) ) {
						unset( $permalink_structure[ $total_element - 1 ] );
						$permalink_structure = implode( '/', $permalink_structure );
						$permalink_structure .= '.html';
						update_option( 'permalink_structure', $permalink_structure );
					}
				}
			} else {
				$old_permalink_structure = get_option( 'old_permalink_structure' );
				$permalink_structure = get_option( 'permalink_structure' );
				if ( ! empty( $old_permalink_structure ) && strpos( $permalink_structure , '.html' ) ) {
					update_option( 'permalink_structure', $old_permalink_structure );
				}
			}
			$wp_rewrite->flush_rules();

			update_option( 'hfa_selected_post_type', $selected_post_type );
		}
		?>
		<div class='hfa_setting_containre'>
			<div class="hfa_pluing_information">
				<p class='hfa_info update'> In terms of SEO and ranking, there is little benefit to keeping the .html extension present in your URLs. </p>
			</div>
			<div class="hfa_setting_form">
				<form method='post'>
				<?php
					$args = array(
					   'public'   => true,
					   '_builtin' => false,
					);
					$post_types = get_post_types( $args );
					$restricted_post_types = array( 'post', 'page' );
					$post_types = array_merge( $restricted_post_types, $post_types );
				if ( ! empty( $post_types ) ) {
					$selected_post_type = get_option( 'hfa_selected_post_type' );
					echo "<ul  class='post_types_lists'>";
					foreach ( $post_types as $post_type ) {
						$checked = '';
						if ( ! empty( $selected_post_type ) && in_array( $post_type, $selected_post_type ) ) {
							$checked = 'checked';
						}
						$post_type_name = strtoupper( $post_type );
						$post_type_name = str_replace( '_', ' ', $post_type_name );
						echo '<li>';
							echo '<input type="checkbox" ' . esc_html( $checked ) . ' name="hfa_post_types[ ]" value="' . esc_html( $post_type ) . '">';
							echo '<label>' . esc_html( $post_type_name ) . '</Label>';
						echo '</li>';
					}
					echo '<li> <input type="submit" name="hfa_save_post_types" class="button  button-primary button-large" value="Save"> </li>';
					echo '</ul>';
				}
				?>
				</form>
			</div>
		</div>
	<?php }
}
