<?php
/*
Plugin Name: Latest Posts
Plugin URI: https://wordpress.org/plugins/latest-posts
Description: Latest potst widget to display recent posts.
Author: ShapedTheme
Author URI: http://shapedtheme.com
Version: 1.1
License: GPL2
*/

// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

//Style
function st_register_widget_styles() {
	wp_register_style( 'latest-posts', plugins_url( 'latest-posts/style.css' ) );
	wp_enqueue_style( 'latest-posts' );
}

add_action( 'wp_enqueue_scripts', 'st_register_widget_styles' );

//Thumb size
function st_thumb_setup() {

        // image size
        add_image_size('xs-thumb', 64, 64, TRUE);

    }
add_action('after_setup_theme', 'st_thumb_setup');


//wadget
add_action('widgets_init','register_shapedtheme_latest_posts_widget');

function register_shapedtheme_latest_posts_widget()
{
	register_widget('ST_Latest_Posts_Widget');
}

class ST_Latest_Posts_Widget extends WP_Widget{

	function ST_Latest_Posts_Widget()
	{
		$this->WP_Widget( 'st_latest_posts_widget','Latest Posts Widget',array('description' => 'Latest posts widget to display recent posts'));
	}


	/*-------------------------------------------------------
	 *				Front-end display of widget
	 *-------------------------------------------------------*/

	function widget($args, $instance)
	{
		extract($args);

		$title 			= apply_filters('widget_title', $instance['title'] );
		$count 			= $instance['count'];
		$cat_ID 		= $instance['cat_name'];
		
		echo $before_widget;

		$output = '';

		if ( $title )
			echo $before_title . $title . $after_title;

		global $post;


		$args = array( 
			'posts_per_page' 	=> $count,
			'category'			=> $cat_ID
		);

		$posts = get_posts( $args );

		if(count($posts)>0){
			$output .='<div class="latest-posts">';

			foreach ($posts as $post): setup_postdata($post);
				$output .='<div class="media">';

					if(has_post_thumbnail()):
						$output .='<div class="pull-left">';
						$output .='<a href="'.get_permalink().'">'.get_the_post_thumbnail($post->ID, 'xs-thumb', array('class' => 'img-responsive')).'</a>';
						$output .='</div>';
					endif;

					$output .='<div class="media-body">';
					$output .= '<h3 class="entry-title"><a href="'.get_permalink().'">'. get_the_title() .'</a></h3>';
					$output .= '<div class="entry-meta small"><span class="st-lp-time">'. get_the_time() . '</span> <span clss="st-lp-date">' . get_the_date('d M Y') . '</span></div>';
					$output .='</div>';

				$output .='</div>';
			endforeach;

			wp_reset_query();

			$output .='</div>';
		}


		echo $output;

		echo $after_widget;
	}


	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['cat_name'] 		= strip_tags( $new_instance['cat_name'] );
		$instance['count'] 			= strip_tags( $new_instance['count'] );

		return $instance;
	}


	function form($instance)
	{
		$defaults = array( 
			'title' 	=> 'Latest Posts',
			'cat_name' 	=> ' ',
			'count' 	=> 5
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_name' ); ?>">Select Category</label>
			<?php 
				$categories = get_categories(array('hierarchical' => false));
				if(isset($instance['cat_name'])) $cat_ID = $instance['cat_name'];
			?>
			<select class="widefat" id="<?php echo $this->get_field_id( 'cat_name' ); ?>" name="<?php echo $this->get_field_name( 'cat_name' ); ?>">
				<?php
				$op = '<option value="%s"%s>%s</option>';

				foreach ($categories as $category ) {

					if ($cat_ID === $category->cat_ID) {
			            printf($op, $category->cat_ID, ' selected="selected"', $category->name);
			        } else {
			            printf($op, $category->cat_ID, '', $category->name);
			        }
			    }
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>">Count</label>
			<input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}