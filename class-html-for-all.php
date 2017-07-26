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
		add_filter( 'post_link', array( $this, 'post_link_callback' ),99,3 );

		add_filter( 'redirect_canonical', '__return_false' );
		add_action( 'rewrite_rules_array', array($this, 'rewrite_rules' )) ;
		add_filter( 'post_type_link',array($this, 'custom_post_permalink_test' ), 10, 1 ); 
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
		if ( $wp_rewrite->using_permalinks() && true === $wp_rewrite->use_trailing_slashes && 'page' === $type ) {
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

	function post_link_callback( $permalink, $post, $leavename ){
		global $post;
		$type = get_post_type( $post->ID );
		return home_url( $post->post_name . '.html' );
	}


	function rewrite_rules( $rules ) {
		$new_rules = array();
		$post_types = get_post_types();	    
		foreach ( $post_types as $post_type )
			if('post' === $post_type){
				$new_rules['([^/]+)\.html$' ] = 'index.php?post_type=' . $post_type . '&name=$matches[1]';
			}else{
				$new_rules[ $post_type . '/([^/]+)\.html$' ] = 'index.php?post_type=' . $post_type . '&name=$matches[1]';
			}
		return $new_rules + $rules;
	}

	function custom_post_permalink_test( $post_link ) {
		global $post;
		$type = get_post_type( $post->ID );
		return home_url( $type . '/' . $post->post_name . '.html' );
	}
}
