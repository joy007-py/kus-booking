<?php

namespace Kus\Booking;

/**
 * class sendEmail
 * 
 * @author Kus Developer
 */

class sendEmail
{
    /**
     * Send a email to the admin
     * 
     * @param int $post_id
     * @param array $formData
     */
    public static function sendMailToAdmin( $post_id, $formData )
    {
        $to = get_option('admin_email');
        $subject = 'New Service Booking';
        $body = self::generateEmailBody( $formData );
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail( $to, $subject, $body, $headers );
    }

    /**
     * Generate email body
     * 
     * @param array $post
     * @return string
     */
    private static function generateEmailBody( $formData )
    {
        $body = '';
        $body .= '<h3>'. $formData['service_title'] .'</h3>';
        $body .= '<p> Name :'. $formData['name']  .'</p>';
        $body .= '<p> Email : '. $formData['email'] .'</p>';
        $body .= '<p> Date : '. $formData['date'] .'</p>';
        $body .= '<p> Time : '. $formData['time'] .'</p>';
        $body .= '<p> Message : '. $formData['message'] .'</p>';

        return $body;
    }
}