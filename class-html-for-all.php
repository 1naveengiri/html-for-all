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
	private $selected_post_type;
	/**
	 * __construct Html_For_All class constructor
	 */
	function __construct() {
		add_action( 'init', array( $this, 'hfa_page_permalink' ), -1 );
		register_activation_hook( __FILE__, array( $this, 'hfa_active' ) );
		register_deactivation_hook( __FILE__, array( $this, 'hfa_deactive' ) );
		add_filter( 'user_trailingslashit', array( $this, 'hfa_page_slash' ),66,2 );
		add_filter( 'post_link', array( $this, 'post_link_callback' ),99,3 );
		$this->selected_post_type = get_option("hfa_selected_post_type");
		add_filter( 'redirect_canonical', '__return_false' );
		add_action( 'rewrite_rules_array', array($this, 'hfa_rewrite_rules' )) ;
		add_filter( 'post_type_link',array($this, 'hfa_custom_post_permalink' ), 10, 1 ); 
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
		if( in_array( 'page', $this->selected_post_type ) ){
			if ( ! strpos( $wp_rewrite->get_page_permastruct(), '.html' ) ) {
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
			}
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Conditionally adds a trailing slash if the permalink structure has a trailing slash, strips the trailing slash if not. 
	 * @param  string $string URL with or without a trailing slash.
	 * @param  string $type The type of URL being considered (e.g. single, category, etc) for use in the filter.
	 * @return string  $string   Adds/removes a trailing slash based on the permalink structure
	 */
	function hfa_page_slash( $string, $type ) {
		global $wp_rewrite;
		if( in_array( $type, $this->selected_post_type ) ){
			if ( $wp_rewrite->using_permalinks() && true === $wp_rewrite->use_trailing_slashes && 'page' === $type ) {
				$string = untrailingslashit( $string );
			}
		}
		return $string;
	}

	/**
	 * Function to get call when Plugin get activated
	 */
	function hfa_active() {
		global $wp_rewrite;
		if( in_array('page', $this->selected_post_type) ){
			if ( ! strpos( $wp_rewrite->get_page_permastruct(), '.html' ) ) {
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
			}
		}
		$wp_rewrite->flush_rules();
	}

	/**
	 * Function to get call when Plug in get deactivated
	 */
	function hfa_deactive() {
		global $wp_rewrite;
		if( in_array('page', $this->selected_post_type) ){
			$wp_rewrite->page_structure = str_replace( '.html','',$wp_rewrite->page_structure );
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Add .html in post URL.
	 * 
	 * @param  string $post_link Post permalink structure
	 * @return  string $new_permalink New permalink structure for post with .html at the end. 
	 */
	function post_link_callback( $permalink, $post, $leavename ){
		global $post;
		$type = get_post_type( $post->ID );
		if( in_array($type, $this->selected_post_type) ){
			$permalink = home_url( $post->post_name . '.html' );
		}
		return $permalink;
	}

	/**
	 * Add rewrite rules for all post, CPT
	 * 
	 * @param  array $rules Rules defined for post URL 
	 * @return  array $new_rules New rules defined for post and custom post with .html extension. 
	 */
	function hfa_rewrite_rules( $rules ) {
		$new_rules = array();
		$post_types = get_post_types();	    
		foreach ( $post_types as $post_type )
			if( in_array($post_type, $this->selected_post_type) ){
				if('post' === $post_type){
					$new_rules['([^/]+)\.html$' ] = 'index.php?post_type=' . $post_type . '&name=$matches[1]';
				}else{
					$new_rules[ $post_type . '/([^/]+)\.html$' ] = 'index.php?post_type=' . $post_type . '&name=$matches[1]';
				}
			}
		return $new_rules + $rules;
	}

	/**
	 * Add .html in custom post URL.
	 * 
	 * @param  string $post_link Post permalink structure
	 * @return  string $post_link New permalink structure for post with .html at the end. 
	 */
	function hfa_custom_post_permalink( $post_link ) {
		global $post;
		$type = get_post_type( $post->ID );
		if( in_array($type, $this->selected_post_type) ){
			$post_link = home_url( $type . '/' . $post->post_name . '.html' );
		}
		return $post_link;
	}
}
