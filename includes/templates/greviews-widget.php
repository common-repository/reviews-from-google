<?php

class Greviews_Widget extends WP_Widget {
	// class constructor
	public function __construct() {
    $widget_ops = array( 
      'classname' => 'greviews_widget',
      'description' => 'A widget to display Google Reviews',
    );
    parent::__construct( 'greviews_widget', 'Google Reviews Widget', $widget_ops );
  }
	
	// output the widget content on the front-end
	public function widget( $args, $instance ) {
    global $greviews_options;

    echo $args['before_widget'];

    $reviewsDB = new Stored_Reviews_WP();
    $reviews = $reviewsDB->get_reviews(5);

    if(count($reviews) > 0) {
      $output = $reviewsDB->widgetHeader();
    
      $output .= $reviewsDB->reviewsHTML(5, 0, 'widget', $instance);
  
      $output .= $reviewsDB->generateMoreLink('widget');
      $output .= '</div>';
    } else {
      if($greviews_options['key'] == '' || $greviews_options['id'] == '') {
        $output = "<p>You need to configure the google reviews plugin, you can do that <a href='/wp-admin/options-general.php?page=greviews-options'>here</a>.</p>";
      } else {
        $output = "<p>There are no reviews to display.</p>";
      }
    }
    
    wp_enqueue_script('greviews_read_more_less');

    echo $output;
    echo $args['after_widget'];
  }
  
	// output the option form field in admin Widgets screen
	public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'greviews' );
    $lowest_allowed_rating = ! empty( $instance['lowest_rating'] ) ? $instance['lowest_rating'] : esc_html__( '5', 'greviews' );

    ?>
      <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
    <?php esc_attr_e( 'Title:', 'greviews' ); ?>
      </label> 
      
      <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $title ); ?>">
      </p>

    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'lowest_rating' ) ); ?>">
    <?php esc_attr_e( 'Lowest Rating to Display:', 'greviews' ); ?>
      </label> 
      
      <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'lowest_rating' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'lowest_rating' ) ); ?>" 
        type="number" 
        value="<?php echo esc_attr( $lowest_allowed_rating ); ?>">
    </p>

    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'hide_images' ) ); ?>">
    <?php esc_attr_e( 'Hide Images:', 'greviews' ); ?>
      </label> 
      
      <select 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'hide_images' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'hide_images' ) ); ?>"  
      >
        <option <?php selected($instance['hide_images'], 'Yes') ?> value=<?php echo esc_html__('Yes', 'greviews'); ?>><?php echo esc_html__('Yes', 'greviews'); ?></option>
        <option <?php selected($instance['hide_images'], 'No') ?> value=<?php echo esc_html__('No', 'greviews'); ?>><?php echo esc_html__('No', 'greviews'); ?></option>
      </select>
    </p>
    <?php
  }

	// save options
	public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['hide_images'] = ( ! empty( $new_instance['hide_images'] ) ) ? strip_tags( $new_instance['hide_images'] ) : '';
    $instance['lowest_rating'] = ( ! empty( $new_instance['lowest_rating'] ) ) ? strip_tags( $new_instance['lowest_rating'] ) : '';
  
    return $instance;
  }
}

function greviews_widget() {
  register_widget('Greviews_Widget');
}

// register widget
add_action('widgets_init', 'greviews_widget');