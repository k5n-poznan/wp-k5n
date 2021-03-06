<?php

if (!defined('ABSPATH')) {
    exit;
}

include_once dirname(__FILE__) . "/includes/classes/excel-reader.class.php";

global $wpdb, $table_prefix;
$get_mobile = $wpdb->get_col("SELECT `mobile` FROM {$table_prefix}k5n_subscribes");
$result = [];
$duplicate = [];

if (isset($_POST['wps_import'])) {
    if (!$_FILES['wps-import-file']['error']) {

        $data = new Spreadsheet_Excel_Reader($_FILES["wps-import-file"]["tmp_name"]);

        foreach ($data->sheets[0]['cells'] as $items) {

            // Check and count duplicate items
            if (in_array($items[3], $get_mobile)) {
                $duplicate[] = $items[3];
                continue;
            }

            // Count submitted items.
            $total_submit[] = $data->sheets[0]['cells'];

            $result = $wpdb->insert("{$table_prefix}k5n_subscribes", array(
                'date' => WP_K5N_CURRENT_DATE,
                'name' => $items[1],
                'surname' => $items[2],
                'mobile' => $items[3],
                'status' => '1',
                'group_ID' => $_POST['wpk5n_group_name']
                    )
            );
        }

        if ($result) {
            echo "<div class='updated'><p>" . sprintf(__('<strong>%s</strong> pozycje zostały pomyślnie dodane.', 'wp-k5n'), count($total_submit)) . "</div></p>";
        }

        if ($duplicate) {
            echo "<div class='error'><p>" . sprintf(__('<strong>%s</strong> powtórzony numer telefonu.', 'wp-k5n'), count($duplicate)) . "</div></p>";
        }
    } else {
        echo "<div class='error'><p>" . __('Proszę wypełnić wszystkie pola', 'wp-k5n') . "</div></p>";
    }
}