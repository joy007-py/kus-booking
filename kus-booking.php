<?php

/**
 * Plugin Name: Kus Booking
 * Version: 1.0.0
 * Requires at least: 5.1
 * Requires PHP: 7.0
 * Description: A small plugin for handle service booking
 * Author: Kus Software
 * Author URI: http://kussoft.com
 */

/**
 * Make sure that wordpress is loaded
 */
if( ! defined('ABSPATH') )
{
    exit;
}

define('KUS_BOOKING_PLUGIN_FILE_DIR', __FILE__);
define('KUS_BOOKING_POST_TYPE_TO_WORK_WITH', 'service');

require __DIR__ . '/vendor/autoload.php';

use  Kus\Booking\Booking;

function fireUpKusBookingPlugin() {
    Booking::getInstance();
}

/**
 * Let's go!
 */
fireUpKusBookingPlugin();
