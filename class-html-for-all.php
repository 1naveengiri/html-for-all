<?php
/**
 * FIle to manage apply .html  append to posts URLs.
 */
namespace BUDDY_SEO_URL;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Html_For_All  Allow to Add .html for all kind of post, pages, cpt.
 */
class Html_For_All {
	/**
	 *  Html_For_All class instance
	 *
	 *  @var private static $instance
	 */
	private static $instance;

	/**
	 * __construct Html_For_All class constructor
	 */
	function __construct() {
		add_action( 'init', array( $this, 'hfa_page_permalink' ), -1 );
		register_activation_hook( __FILE__, array( $this, 'hfa_active' ) );
		register_deactivation_hook( __FILE__, array( $this, 'hfa_deactive' ) );
		add_filter( 'user_trailingslashit', array( $this, 'hfa_page_slash' ),66,2 );
		// add_filter( 'post_rewrite_rules', array( $this, 'post_rewrite_rules_callback' ),99,1);
	}

	/**
	 * Get class instance in singleton way.
	 *
	 * @return object Html_For_All::$instance
	 */
	public static function get_instance() {
		if ( empty( SELF::$instance ) ) {
			$instance = new Html_For_All();
		}
		return SELF::$instance;
	}

	/**
	 * Function to add .html at the end of file.
	 */
	function hfa_page_permalink() {
		global $wp_rewrite;
		if ( ! strpos( $wp_rewrite->get_page_permastruct(), '.html' ) ) {
			$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';

		}
		$wp_rewrite->flush_rules();
	}

	function hfa_page_slash( $string, $type ) {
		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() && true === $wp_rewrite->use_trailing_slashes && 'page' === $type && 'post' === $type ) {
			return untrailingslashit( $string );
		} else {
			return $string;
		}
	}

	/**
	 * Function to get call when Plugin get activated
	 */
	function hfa_active() {
		global $wp_rewrite;
		if ( ! strpos( $wp_rewrite->get_page_permastruct(), '.html' ) ) {
			$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
		}
		$wp_rewrite->flush_rules();
	}

	/**
	 * Function to get call when Plug in get deactivated
	 */
	function hfa_deactive() {
		global $wp_rewrite;
		$wp_rewrite->page_structure = str_replace( '.html','',$wp_rewrite->page_structure );
		$wp_rewrite->flush_rules();
	}

	/**
	 * Function to add page rewrite rules for post and CPTs
	 */
	function post_rewrite_rules_callback( $post_rewrite ){
		// echo "asdsad";
		// var_export($post_rewrite);
		$post_rewrite->permalink_structure =  '%postname%.html';
	}
}

