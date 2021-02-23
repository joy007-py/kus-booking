<?php

namespace Kus\Booking;

/**
 * class KusBooking
 * 
 * @author Kus Developer
 */
class Booking
{
    private static $_instance;

    private function __construct()
    {
        \add_action('wp_footer', array($this, 'popupFormTemplate'));
        \add_action('widgets_init', function() {
            register_widget( BookingWidget::class );
        });
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
                            <h5 class="modal-title" id="exampleModalLabel">Book</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="serviceName" class="form-label">Choose Service</label>
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            <div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputPassword1" class="form-label">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword1">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">Check me out</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
}