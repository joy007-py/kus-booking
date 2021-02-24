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
    const SERVICE_BOOKING_POST_TYPE = 'service_bookings';

    private static $_instance;

    private function __construct()
    {
        // registering custom post type
        \add_action( 'init', array( $this, 'addServiceBookingCustomPostType') );
        
        // inject popup html code in footer
        \add_action('wp_footer', array($this, 'popupFormTemplate'));
        
        // registering widgets
        \add_action('widgets_init', function() {
            register_widget( BookingWidget::class );
        });

        \add_action('kus_booking_dropdown_service_list', array( PluginHelper::class, 'genertaeServiceDropDown' ), 100 );
        \add_filter('kus_booking_popup_header', array($this, 'getTheServiceTitle'), 10, 1);
        
        // for loading stylesheets and javascript
        \add_action('wp_enqueue_scripts', array($this, 'loadScripts'));

        // for ajax
        \add_action('wp_ajax_nopriv_' . self::AJAX_FORM_ACTION, array($this, 'handleFormData'));
        \add_action('wp_ajax_' . self::AJAX_FORM_ACTION, array($this, 'handleFormData'));

        // on plugin activation and deactivation
        \register_activation_hook( KUS_BOOKING_PLUGIN_FILE_DIR, array($this, 'onActivate') );
        \register_deactivation_hook( KUS_BOOKING_PLUGIN_FILE_DIR, array($this, 'onDeactivate'));

        // fire when we got a new booking
        \add_action('kus_booking_after_booked_confirm' , array( sendEmail::class , 'sendMailToAdmin'), 10, 2);
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

            wp_enqueue_style(
                'kus_booking_main',
                plugins_url('/assets/css/kus_booking_main.css', KUS_BOOKING_PLUGIN_FILE_DIR)
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
                <!-- The Modal -->
                <div id="kus_booking_form_modal" class="kus_booking_form">
                    <!-- Modal content -->
                    <div class="modal-content" id="kus_booking_modal_content">

                        <h5 class="modal-title" id="kus_booking_form_header">
                            <?php echo \apply_filters('kus_booking_popup_header', 'Book a service') ?>
                        </h5>
                        <span class="close" id="kus_booking_form_modal_close">&times;</span>
                        <form method="POST" id="kus_booking_form" data-url = <?php echo \admin_url('admin-ajax.php') ?>>       
                            <div class="">
                                <label for="serviceName" class="form-label">Or choose Service</label>
                                <?php do_action('kus_booking_dropdown_service_list') ?>
                            <div>
                            <div class="">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="kus_booking_name">
                            </div>
                            <div class="">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" name="kus_booking_email">
                            </div>
                            <div class="">
                                <label for="date" class="form-label">Booking Date</label>
                                <input type="date" class="form-control" name="kus_booking_date">
                            </div>
                            <div class="">
                                <label for="date" class="form-label">Booking Time</label>
                                <input type="time" class="form-control" name="kus_booking_time">
                            </div>
                            <div class="">
                                <label for="date" class="form-label">Message</label>
                                <textarea name="kus_booking_message" rows="5" cols="20"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" id="kus_booking_btn_submit">Book</button>
                        </form>
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
     * Handle Ajax
     */
    public function handleFormData()
    {
        $formData = \file_get_contents('php://input');

        $formData = json_decode($formData);

        $service_title = '#Booking For - ' . trim($formData->book_title);
        $name = trim($formData->name);
        $email = trim($formData->email);
        $date = $formData->date;
        $time = $formData->time;
        $message = $formData->message;

        $new_booking = array(
            'post_title' => $service_title,
            'post_type' => self::SERVICE_BOOKING_POST_TYPE,
            'post_status' => 'publish',
        );
        
        $post_id = wp_insert_post( $new_booking );

        header('Content-Type: application/json');

        if( \is_wp_error($post_id) )
        {
            echo json_encode(
                array(
                    'success' => false,
                    'message' => 'post insert failed'
                )
            );

            wp_die();
        }

        add_post_meta( $post_id, 'name', $name );
        add_post_meta( $post_id, 'email', $email );
        add_post_meta( $post_id, 'date', $date );
        add_post_meta( $post_id, 'time', $time );
        add_post_meta( $post_id, 'message', $message );

        do_action(
            'kus_booking_after_booked_confirm',
            $post_id,
            array(
                'service_title' => $service_title,
                'name' => $name,
                'email' => $email,
                'date' => $date,
                'time' => $time,
                'message' => $message
            )
        );

        echo json_encode(
            array(
                'success' => true,
                'post_id' => $post_id
            )
        );

        wp_die();
    }

    /**
     * Register custom post type
     */
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
            "supports" => [ "title", "custom-fields"],
        ];
    
        register_post_type( self::SERVICE_BOOKING_POST_TYPE, $args );
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