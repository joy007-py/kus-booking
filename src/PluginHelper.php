<?php

namespace Kus\Booking;

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
}