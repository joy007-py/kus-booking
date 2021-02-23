<?php

namespace Kus\Booking;

/**
 * class KusBooking
 * 
 * @author Kus Developer
 */
class Booking
{
    
    const AJAX_FORM_ACTION = 'kus_booking_form_data';

    private static $_instance;

    private function __construct()
    {
        \add_action( 'init', array( $this, 'addServiceBookingCustomPostType') );
        \add_action('wp_footer', array($this, 'popupFormTemplate'));
        \add_action('widgets_init', function() {
            register_widget( BookingWidget::class );
        });
        \add_action('kus_booking_dropdown_service_list', array( PluginHelper::class, 'genertaeServiceDropDown' ), 100 );
        \add_filter('kus_booking_popup_header', array($this, 'getTheServiceTitle'), 10, 1);
        \add_action('wp_enqueue_scripts', array($this, 'loadScripts'));
        \add_action('wp_ajax_nopriv_' . self::AJAX_FORM_ACTION, array($this, 'handleFormData'));
        \add_action('wp_ajax_' . self::AJAX_FORM_ACTION, array($this, 'handleFormData'));
        \register_activation_hook( KUS_BOOKING_PLUGIN_FILE_DIR, array($this, 'onActivate') );
        \register_deactivation_hook( KUS_BOOKING_PLUGIN_FILE_DIR, array($this, 'onDeactivate'));
    }   

    /**
     * Get plugin instance
     */
    public static function getInstance()
    {
        if( \is_null (self::$_instance) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Load our stylesheet and javasripts
     */
    public function loadScripts()
    {
        // load only if we are on service page
        if( PluginHelper::isCurrentPostTypeIsService() )
        {
            \wp_enqueue_script(
                'kus_booking', 
                plugins_url('/assets/js/kus_booking.js', KUS_BOOKING_PLUGIN_FILE_DIR),
                [],
                '',
                true
            );
        }
    }

    /**
     * Render the booking pop up form
     */
    public function popupFormTemplate()
    {
        if (PluginHelper::isCurrentPostTypeIsService())
        {
            ?>
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kus_booking_form_header">
                                <?php echo \apply_filters('kus_booking_popup_header', 'Book a service') ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        <form method="POST" id="kus_booking_form" data-url = <?php echo \admin_url('admin-ajax.php') ?>>
                            <div class="mb-3">
                                <label for="serviceName" class="form-label">Or choose Service</label>
                                <?php do_action('kus_booking_dropdown_service_list') ?>
                            <div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="" name="kus_booking_name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" name="kus_booking_email">
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Booking Date</label>
                                <input type="date" class="form-control" name="kus_booking_date">
                            </div>
                            <button type="submit" class="btn btn-primary">Book</button>
                        </form>
                        </div>
                        <div class="modal-footer">
                            <!--
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            -->
                        </div>
                        </div>
                    </div>
                </div>
            <?php
        }
    }

    /**
     * Modify popup header
     * 
     * @param string $title
     */
    public function getTheServiceTitle( $title )
    {
        $current_service = \get_the_title();

        $title = 'Book For - ' . $current_service;

        return $title;
    }

    /**
     * handle Ajax
     */
    public function handleFormData()
    {
        $formData = \file_get_contents('php://input');

        $formData = json_decode($formData);

        \file_put_contents( __DIR__ . '/log.txt', var_export($formData, true));

        echo json_encode(
            array(
                'data' => true
            )
        );
    }

    public function addServiceBookingCustomPostType()
    {
        $labels = [
            "name" => __( "Service Bookings"),
            "singular_name" => __( "Service Booking"),
        ];
    
        $args = [
            "label" => __( "Service Bookings"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "show_ui" => true,
            "publicly_queryable" => true,
            "show_in_rest" => true,
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "query_var" => true,
            "supports" => [ "title", "editor", "custom-fields"],
        ];
    
        register_post_type( "service_bookings", $args );
    }

    /**
     * Runs on plugin activation
     */
    public function onActivate()
    {
        $this->addServiceBookingCustomPostType();
        \flush_rewrite_rules();
    }

    /**
     * Runs on plugin deactivation
     */
    public function onDeactivate()
    {
        unregister_post_type( 'service_bookings' );
        \flush_rewrite_rules();
    }
}