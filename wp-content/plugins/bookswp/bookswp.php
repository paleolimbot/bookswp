<?php
/**
 * @package BooksWP
 * @version 0.1
 */
/*
Plugin Name: BooksWP
Plugin URI: http://github.com/paleolimbot/bookswp
Description: Use WordPress to keep a personal catalog of books
Author: Dewey Dunnington
Version: 0.1
Author URI: http://www.fishandwhistle.net/
*/

// create two taxonomies, genres and writers for the post type "book"
function bookswp_create_taxonomies() {
    
        // Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Shelves', 'taxonomy general name', 'text_domain' ),
		'singular_name'     => _x( 'Shelf', 'taxonomy singular name', 'text_domain' ),
		'search_items'      => __( 'Search Shelves', 'text_domain' ),
		'all_items'         => __( 'All Shelves', 'text_domain' ),
		'parent_item'       => __( 'Parent Shelf', 'text_domain' ),
		'parent_item_colon' => __( 'Parent Shelf:', 'text_domain' ),
		'edit_item'         => __( 'Edit Shelf', 'text_domain' ),
		'update_item'       => __( 'Update Shelf', 'text_domain' ),
		'add_new_item'      => __( 'Add New Shelf', 'text_domain' ),
		'new_item_name'     => __( 'New Shelf Name', 'text_domain' ),
		'menu_name'         => __( 'Shelves', 'text_domain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'shelf' ),
	);

	register_taxonomy( 'shelves', array( 'book' ), $args );
    
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Genres', 'taxonomy general name', 'text_domain' ),
		'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'text_domain' ),
		'search_items'      => __( 'Search Genres', 'text_domain' ),
		'all_items'         => __( 'All Genres', 'text_domain' ),
		'parent_item'       => __( 'Parent Genre', 'text_domain' ),
		'parent_item_colon' => __( 'Parent Genre:', 'text_domain' ),
		'edit_item'         => __( 'Edit Genre', 'text_domain' ),
		'update_item'       => __( 'Update Genre', 'text_domain' ),
		'add_new_item'      => __( 'Add New Genre', 'text_domain' ),
		'new_item_name'     => __( 'New Genre Name', 'text_domain' ),
		'menu_name'         => __( 'Genres', 'text_domain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'genre' ),
	);

	register_taxonomy( 'genres', array( 'book' ), $args );

	// Add new taxonomy, NOT hierarchical (like tags)
	$labels = array(
		'name'                       => _x( 'People', 'taxonomy general name', 'text_domain' ),
		'singular_name'              => _x( 'Person', 'taxonomy singular name', 'text_domain' ),
		'search_items'               => __( 'Search People', 'text_domain' ),
		'popular_items'              => __( 'Popular People', 'text_domain' ),
		'all_items'                  => __( 'All People', 'text_domain' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Person', 'text_domain' ),
		'update_item'                => __( 'Update Person', 'text_domain' ),
		'add_new_item'               => __( 'Add New Person', 'text_domain' ),
		'new_item_name'              => __( 'New Person Name', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate people with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove people', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used people', 'text_domain' ),
		'not_found'                  => __( 'No people found.', 'text_domain' ),
		'menu_name'                  => __( 'People', 'text_domain' ),
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'people' ),
	);

	register_taxonomy( 'people', 'book', $args );
        
        // Add new taxonomy, NOT hierarchical (like tags)
	$labels = array(
		'name'                       => _x( 'Keywords', 'taxonomy general name', 'text_domain' ),
		'singular_name'              => _x( 'Keyword', 'taxonomy singular name', 'text_domain' ),
		'search_items'               => __( 'Search Keywords', 'text_domain' ),
		'popular_items'              => __( 'Popular Keywords', 'text_domain' ),
		'all_items'                  => __( 'All Keywords', 'text_domain' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Keyword', 'text_domain' ),
		'update_item'                => __( 'Update Keyword', 'text_domain' ),
		'add_new_item'               => __( 'Add New Keyword', 'text_domain' ),
		'new_item_name'              => __( 'New Keyword Name', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate keywords with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove keywords', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used keywords', 'text_domain' ),
		'not_found'                  => __( 'No keywords found.', 'text_domain' ),
		'menu_name'                  => __( 'Keywords', 'text_domain' ),
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'keyword' ),
	);

	register_taxonomy( 'keywords', 'book', $args );
}

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'bookswp_create_taxonomies', 0 );

function bookswp_create_post_type() {
    // Register Custom Post Type

    $labels = array(
            'name'                  => _x( 'Books', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Books', 'text_domain' ),
            'name_admin_bar'        => __( 'Book', 'text_domain' ),
            'archives'              => __( 'Book Archives', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
            'all_items'             => __( 'All Books', 'text_domain' ),
            'add_new_item'          => __( 'Add New Book', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Book', 'text_domain' ),
            'edit_item'             => __( 'Edit Book', 'text_domain' ),
            'update_item'           => __( 'Update Book', 'text_domain' ),
            'view_item'             => __( 'View Book', 'text_domain' ),
            'search_items'          => __( 'Search Books', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
            'items_list'            => __( 'Items list', 'text_domain' ),
            'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $rewrite = array(
            'slug'                  => 'book'
    );
    $args = array(
            'label'                 => __( 'Book', 'text_domain' ),
            'description'           => __( 'Book type.', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', ),
            'taxonomies'            => array( 'people', 'genres', ' keywords', 'shelves' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_icon'             => 'dashicons-book',
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'books',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'post',
    );
    register_post_type( 'book', $args );

}
add_action( 'init', 'bookswp_create_post_type', 0);


// Add/remove 'book' posts from the main query depending on options
function bookswp_add_query_vars_filter( $vars ){
  $vars[] = "booksearch";
  return $vars;
}
add_filter( 'query_vars', 'bookswp_add_query_vars_filter' );

function bookswp_add_books_to_query( $query ) {
    //exclude admin queries always
    if($query->is_admin) {
        return $query;
    }
    $currenttypes = $query->query_vars['post_type'];
    if(!is_array($currenttypes)) {
        $currenttypes = array($currenttypes);
    }
    if(get_option('bookswp_books_like_posts')) {
        if($query->query_vars['booksearch'] && $query->is_main_query()) {
            $query->set( 'post_type',  'book' );
        } else if ( (is_home() || is_archive() || is_search()) && 
                $query->is_main_query() && !in_array('book', $currenttypes)) {
            // add books to post type
            $newtypes = in_array('post', $currenttypes) ? array('book') : array('post', 'book');
            $query->set( 'post_type',  array_merge($currenttypes , $newtypes));
        }
    } else {
        if($query->query_vars['booksearch'] && $query->is_main_query()) {
            $query->set( 'post_type',  'book' );
        } else if ( (is_search()) && $query->is_main_query()) {
            // remove books from post type
            $query->set( 'post_type',  'post' );
        }
    }
  return $query;
}
add_action( 'pre_get_posts', 'bookswp_add_books_to_query' );


/**
 * Extend WordPress search to include custom fields
 * http://adambalee.com
 * Join posts and postmeta tables: http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 * Modify the search query with posts_where: http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 * Prevent duplicates: http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 * 
 * Could query only books by using global $wp_query, but this would restrict administrator
 * functionality. Leaving this as is for now.
 */

function bookswp_search_join( $join ) {
    global $wpdb;
    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}
function bookswp_search_where( $where ) {
    global $wpdb;
    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }
    return $where;
}
function bookswp_search_distinct( $where ) {
    if ( is_search() ) {
        return "DISTINCT";
    }
    return $where;
}
if(get_option('bookswp_search_postmeta', true)) {
    add_filter('posts_join', 'bookswp_search_join' );
    add_filter( 'posts_where', 'bookswp_search_where' );
    add_filter( 'posts_distinct', 'bookswp_search_distinct' );
}


//this filter modifies the get archives query so that the appropriate dates
//are shown in the archive widget
function bookswp_getarchives_where( $where ){
    return str_replace( "post_type = 'post'", "post_type IN ( 'post', 'book' )", $where );
}
if(get_option('bookswp_books_like_posts')) {
    add_filter( 'getarchives_where', 'bookswp_getarchives_where' );
}

// register settings page 
function bookswp_register_mysettings() { // whitelist options
  register_setting( 'bookswp-usersettings', 'bookswp_books_like_posts' );
  register_setting( 'bookswp-usersettings', 'bookswp_search_postmeta' );
  register_setting( 'bookswp-usersettings', 'bookswp_goodreads_api' );
}
function bookswp_settings_page() {
    include plugin_dir_path(__FILE__) . '/options.php';
}
function bookswp_create_settings_menu() {
    //create new top-level menu
    add_options_page('BooksWP Settings', 'BooksWP', 'administrator', 'bookswp-settings', 
            'bookswp_settings_page' );
}
add_action('admin_menu', 'bookswp_create_settings_menu');

if ( is_admin() ){ // admin actions
  add_action( 'admin_menu', 'bookswp_create_settings_menu' );
  add_action( 'admin_init', 'bookswp_register_mysettings' );
}

// register add by goodreads on the post edit page
require_once plugin_dir_path(__FILE__) . '/bookapis.php';
add_action( 'edit_form_top', 'bookswp_do_goodreads_lookup');
add_action( 'admin_notices', 'bookswp_quick_add_book_form' );
add_action( 'admin_head', 'bookswp_quick_add_css' );
add_action( 'wp_head', 'bookswp_quick_add_css' );


// register Booksearch_Widget and QuickAdd_Widget
require_once plugin_dir_path(__FILE__) . '/booksearch.php';
function bookswp_register_widgets() {
    register_widget( 'Booksearch_Widget' );
    register_widget( 'QuickAdd_Widget' );
}
add_action( 'widgets_init', 'bookswp_register_widgets' );

