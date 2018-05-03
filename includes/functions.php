<?php

if (!defined('ABSPATH')) {
    exit;
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
