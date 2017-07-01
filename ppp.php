<?php
/*
Plugin Name: Popular Posts Plugin - PPP
Plugin URI:  https://github.com/Nickiam7/popular-posts-plugin/
Description: Popular posts plugin based on number of posts views.
Version:     0.1.0
Author:      Nick McNeany
Author URI:  https://github.com/Nickiam7/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/**
* Post poularity counter
*/
function ppp_popular_post_view( $postID ) {
	$total_key = 'views';
	$view_count = get_post_meta( $postID, $total_key, true );
	if( $view_count == '') {
		delete_post_meta( $postID, $total_key );
		add_post_meta( $postID, $total_key, '0' ); 
	} else {
		$view_count ++;
		update_post_meta( $postID, $total_key, $view_count );
	}
}
/**
* Inject post counter into single post
*/
function ppp_popular_post_counter( $post_id ) {
	if( ! is_single() ) return;
	if( ! is_user_logged_in() ) {
		if( empty( $post_id ) ) {
			global $post;
			$post_id = $post->ID;
		}
	}
	ppp_popular_post_view( $post_id );
}
add_action( 'wp_head', 'ppp_popular_post_counter' );
/**
* Add popular posts data to all posts table
*/
function ppp_add_views_column( $defaults ) {
	$defaults['post_views'] = 'View Count';
	return $defaults;
}
add_filter( 'manage_posts_columns', 'ppp_add_views_column' );

function ppp_display_views( $column_name ) {
	if( $column_name === 'post_views' ) {
		echo( int ) get_post_meta( get_the_ID(), 'views', true);
	}
}
add_action( 'manage_posts_custom_column', 'ppp_display_views', 5, 2 );




















