<?php

if (is_admin()) {

    $installer_wpk5n_ver = get_option('wpk5n_db_version');

    if ($installer_wpsms_ver < WP_K5N_VERSION) {

        global $wp_statistics_db_version, $table_prefix;

        $create_k5n_subscribes = ( "CREATE TABLE {$table_prefix}k5n_subscribes(
				ID int(10) NOT NULL auto_increment,
				date DATETIME,
				name VARCHAR(20),
                                surname VARCHAR(20),
				mobile VARCHAR(20) NOT NULL,
				status tinyint(1),
                                bulletin tinyint(1),
				activate_key INT(11),
				group_ID int(5),
				PRIMARY KEY(ID)) CHARSET=utf8
			" );

        $create_k5n_subscribes_group = ( "CREATE TABLE {$table_prefix}k5n_subscribes_group(
				ID int(10) NOT NULL auto_increment,
				name VARCHAR(250),
				PRIMARY KEY(ID)) CHARSET=utf8
			" );

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta($create_k5n_subscribes);
        dbDelta($create_k5n_subscribes_group);

        update_option('wp_k5n_db_version', WP_K5N_VERSION);
    }
}
