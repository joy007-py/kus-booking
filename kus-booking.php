<?php

/**
 * Plugin Name: Kus Booking
 * Version: 1.0.0
 * Requires at least: 5.1
 * Requires PHP: 7.0
 * Description: A small plugin for handle service booking
 * Author: Kus Software
 * Author URI: http://kusdemos.com
 */

/**
 * Make sure what wordpress is loaded
 */
if( ! defined('ABSPATH') )
{
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use  Kus\Booking\Booking;

function fireUpKusBookingPlugin() {
    Booking::getInstance();
}

/**
 * Let's begin!
 */
fireUpKusBookingPlugin();
