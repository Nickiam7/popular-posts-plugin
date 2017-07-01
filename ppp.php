<?php
/*
Plugin Name: Popular Posts Plugin - PPP
Plugin URI:  https://github.com/Nickiam7/popular-posts-plugin/
Description: Popular posts plugin based on number of post views.
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


/**
* Add Widget
*/
/**
 * Adds PPP_Widget widget.
 */
class PPP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'ppp_widget', // Base ID
			esc_html__( 'Popular Posts', 'ppp' ), // Name
			array( 'description' => esc_html__( 'Show the most popular posts', 'ppp' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		} 

		$args = array(
			'post_type'           => 'post',
			'posts_per_page'      => 5,
			'meta_key'            => 'views',
			'orderby'             => 'meta_value_num',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true
		);

		//New Query
		$the_query = new WP_Query( $args );
		
		//The Loop
		if ( $the_query->have_posts() ) {
			echo '<ul>';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				echo '<li>'; 
				echo '<a href="' . get_the_permalink() . '" rel="bookmark"> ' ;
				echo get_the_title();
				echo ' (' . get_post_meta( get_the_ID(), 'views', true ) . ')';
				echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
			wp_reset_postdata();
		} else {
			echo "Sorry, no posts.";
		}
		wp_reset_postdata();

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Posts', 'ppp' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'ppp' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class PPP_Widget

// register PPP_Widget widget
function register_ppp_widget() {
    register_widget( 'PPP_Widget' );
}
add_action( 'widgets_init', 'register_ppp_widget' );

















