<?php

namespace Kus\Booking;

/**
 * class BookingWidget
 * 
 * @author Kus Developer
 */
class BookingWidget extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'kus_booking',
            __('Kus Booking Button'),
            array(
                'classname' => 'kus_booking_widget',
                'description' => 'A widget button for take service booking'
            )
        );

        \add_action('render_kus_booking_widget_html', array($this, 'buttonHtml'));
    }

    /**
     * Render our widget to frontend
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		do_action('render_kus_booking_widget_html');

		echo $args['after_widget'];
    }

    /**
     * Button html
     */
    public function buttonHtml()
    {
        if(PluginHelper::isCurrentPostTypeIsService())
        {
            ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Book This Service
            </button>
            <?php
        }
    }
}