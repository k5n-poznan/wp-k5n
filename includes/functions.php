<?php

if (!defined('ABSPATH')) {
    exit;
}


if (!function_exists('initial_message_producer')) {

    /**
     * Initial gateway
     * @return mixed
     */
    function initial_message_producer() {
        global $wpk5n_option;

        // Include default gateway
        include_once dirname(__FILE__) . '/class-wp-k5n-message.php';
        include_once dirname(__FILE__) . '/producers/default.class.php';


        $producer_name = 'basic_k5n_message';

        if (is_file(dirname(__FILE__) . '/producers/' . $producer_name . '.class.php')) {
            include_once dirname(__FILE__) . '/producers/' . $producer_name . '.class.php';
        } else {
            return new Default_Message;
        }

        // Create object from the gateway class
        if ($producer_name == 'default') {
            $mess = new Default_Message();
        } else {
            $mess = new $producer_name;
        }

        // Return message object
        return $mess;
    }

}


if (!function_exists('wps_get_group_by_id')) {

    function wps_get_group_by_id($group_id = null) {
        global $wpdb, $table_prefix;

        $result = $wpdb->get_row($wpdb->prepare("SELECT name FROM {$table_prefix}k5n_subscribes_group WHERE ID = %d", $group_id));

        if ($result) {
            return $result->name;
        }
    }

}

if (!function_exists('wps_get_total_subscribe')) {

    function wps_get_total_subscribe($group_id = null) {
        global $wpdb, $table_prefix;

        if ($group_id) {
            $result = $wpdb->query($wpdb->prepare("SELECT name FROM {$table_prefix}k5n_subscribes WHERE group_ID = %d", $group_id));
        } else {
            $result = $wpdb->query("SELECT name FROM {$table_prefix}k5n_subscribes");
        }

        if ($result) {
            return $result;
        }
    }

}
