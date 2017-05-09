<?php
/*
Plugin Name: .html for all url
Plugin URI: http://www.buddydevelopers.com/
Description: Adds .html to post, pages, CPT in WordPress.
Author: buddydevelopers
Version: 1.0
Author URI: http://www.buddydevelopers.com/
*/

namespace BUDDY_SEO_URL;
/**
 * Class Html_For_All  Allow to Add .html for all kind of post, pages, cpt.
 */
Class Html_For_All{

    function __construct(){
        add_action('init', array($this, 'HFA_page_permalink'), -1);
        register_activation_hook(__FILE__, array($this, 'HFA_active'));
        register_deactivation_hook(__FILE__, array($this, 'HFA_deactive'));
        add_filter('user_trailingslashit', array($this, 'HFA_page_slash'),66,2);
    }

    function HFA_page_permalink() {
        global $wp_rewrite;
        if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')){
            $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
        }
    }

    function HFA_page_slash($string, $type){
        global $wp_rewrite;
        if ($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
            return untrailingslashit($string);
        }else{
            return $string;
        }
    }

    function HFA_active() {
        global $wp_rewrite;
        if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')){
            $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
        }
        $wp_rewrite->flush_rules();
    }
    function HFA_deactive() {
        global $wp_rewrite;
        $wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
        $wp_rewrite->flush_rules();
    }
}
?>