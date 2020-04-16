<?php

/**
 * -------------------------------------------------------------------
 * Developed and maintained by Nayeem
 * -------------------------------------------------------------------
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('message_box')) {

    function message_box($message_type, $close_button = TRUE)
    {
        $CI = &get_instance();
        $message = $CI->session->flashdata($message_type);
        $retval = '';

        if ($message) {
            switch ($message_type) {
                case 'success':
                    $retval = '<script type="text/JavaScript">$(document).ready(function () {'
                        . 'toastr.success("' . $message . '");});</script>';
                    break;
                case 'error':
                    $retval = '<script type="text/JavaScript">$(document).ready(function () {'
                        . 'toastr.error("' . $message . '");});</script>';
                    break;
                case 'info':
                    $retval = '<script type="text/JavaScript">$(document).ready(function () {'
                        . 'toastr.info("' . $message . '");});</script>';
                    break;
                case 'warning':
                    $retval = '<script type="text/JavaScript">$(document).ready(function () {'
                        . 'toastr.warning("' . $message . '");});</script>';
                    break;
            }
            return $retval;
        }
    }
}

if (!function_exists('set_message')) {

    function set_message($type, $message)
    {
        $CI = &get_instance();
        $CI->session->set_flashdata($type, $message);
    }

}
if (!function_exists('show_notification')) {
    function show_notification($users = array())
    {
        $realtime_notification = config_item('realtime_notification');
        if (empty($realtime_notification)) {
            return false;
        }

        if (!is_array($users)) {
            return false;
        }

        if (count($users) == 0) {
            return false;
        }

        if (!class_exists('Pusher')) {
            require_once(APPPATH . 'libraries/Pusher.php');
        }

        $app_key = config_item('pusher_app_key');
        $app_secret = config_item('pusher_app_secret');
        $app_id = config_item('pusher_app_id');

        if ($app_key == "" || $app_secret == "" || $app_id == "") {
            return false;
        }

        $pusher_options = array();
        if (!isset($pusher_options['cluster']) && config_item('pusher_cluster') != '') {
            $pusher_options['cluster'] = config_item('pusher_cluster');
        }
        $pusher = new Pusher(
            $app_key,
            $app_secret,
            $app_id,
            $pusher_options
        );

        $channels = array();
        foreach ($users as $id) {
            array_push($channels, 'notifications-channel-' . $id);
        }
        $channels = array_unique($channels);
        $pusher->trigger($channels, 'notification', array());
    }
}

