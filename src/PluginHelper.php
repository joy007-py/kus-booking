<?php

namespace Kus\Booking;

/**
 * class PluginHelper
 * 
 * @author Kus Developer
 */
class PluginHelper
{
    /**
     * Check whether the current post type is service or not
     * 
     * @return bool
     */
    public static function isCurrentPostTypeIsService()
    {
        $post_type = get_post_type( get_the_ID() );

        if( $post_type === false )
        {
            return false;
        }

        return ($post_type === 'service') ? true : false;
    }

    /**
     * Create service list dropdown
     * 
     * @return string
     */
    public static function genertaeServiceDropDown()
    {
        $current_service_title = \get_the_title();

        $args = array(  
            'post_type' => 'service',
            'post_status' => 'publish',
        );
    
        $services = new \WP_Query( $args );

        \ob_start();

        echo '<select class="form-select" aria-label="service-titles" name="kus_booking_service_title" id="kus_booking_service_title_list">';

        while($services->have_posts())
        {
            $services->the_post();
            if( $current_service_title == the_title('', '', false) )
            {
                echo '<option value = "'. \the_title('', '', false) . '" selected >'. \the_title('', '', false) .'</option>';
            }
            else
            {
                echo '<option value = "'. \the_title('', '', false) . '">'. \the_title('', '', false) .'</option>';
            }
        }
        echo '</select>';

        echo \ob_get_clean();
    }
}