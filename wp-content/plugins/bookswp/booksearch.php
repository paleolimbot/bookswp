<?php

function bookswp_search_book_form() {
    ?>
    <form role="search" method="get" id="searchform" class="searchform" action="<?php echo get_site_url(); ?>">
    <div>
        <label class="screen-reader-text" for="s">Search for:</label>
        <input value="<?php echo get_search_query(); ?>" name="s" id="s" type="text"/>
        <input value="true" name="booksearch" type="hidden"/>
        <input id="searchsubmit" value="Search books" type="submit"/>
    </div>
    </form>
    <?php
}

class Booksearch_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'booksearch',
			'description' => 'Search widget to search only books',
		);
		parent::__construct( 'booksearch', 'Book Search', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
	    // outputs the content of the widget
            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }		
            bookswp_search_book_form();
            echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
            // outputs the options form on admin
            $title = $instance['title'] ;            
            ?>
            <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
	    // processes widget options to be saved
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

            return $instance;
	}
}

