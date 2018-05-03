<?php

/**
 * Plugin Name: WP K5N
 * Plugin URI: http://k5n.pl/
 * Description: A powerful texting plugin for WordPress
 * Version: 1.0.0
 * Author: Waldemar Kłaczyński
 * Text Domain: wp-k5n
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Plugin defines
 */
define('WP_K5N_VERSION', '1.0.1');
define('WP_K5N_DIR_PLUGIN', plugin_dir_url(__FILE__));
define('WP_K5N_ADMIN_URL', get_admin_url());
define('WP_K5N_SITE', 'http://k5n.pl');
define('WP_K5N_MOBILE_REGEX', '/^[\+|\(|\)|\d|\- ]*$/');
define('WP_K5N_CURRENT_DATE', date('Y-m-d H:i:s', current_time('timestamp')));


/**
 * Get plugin options
 */
$wpk5n_option = get_option('wpk5n_settings');

$WP_K5N_Plugin = new WP_K5N_Plugin;


/**
 * Install plugin
 */
register_activation_hook(__FILE__, array('WP_K5N_Plugin', 'install'));

/**
 * Class WP_K5N_Plugin
 */
class WP_K5N_Plugin {

    /**
     * Wordpress Admin url
     *
     * @var string
     */
    public $admin_url = WP_K5N_ADMIN_URL;

    /**
     * Wordpress Database
     *
     * @var string
     */
    protected $db;

    /**
     * Wordpress Table prefix
     *
     * @var string
     */
    protected $tb_prefix;

    /**
     * Options
     *
     * @var string
     */
    protected $options;
    public $setting_page;

    /**
     * Constructors plugin
     *
     * @param  Not param
     */
    public function __construct() {
        global $wpdb, $table_prefix, $wpk5n_option;

        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;
        $this->options = $wpk5n_option;

        // Load text domain
        add_action('init', array($this, 'load_textdomain'));

        __('WP K5N', 'wp-k5n');
        __('A simple and powerful texting plugin for wordpress', 'wp-k5n');

        $this->includes();

        $this->setting_page = new WP_K5N_Settings();

        $this->init();


        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'front_assets'));

        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain('wp-k5n', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Creating plugin tables
     *
     * @param  Not param
     */
    static function install() {
        global $wp_k5n_db_version;

        include_once dirname(__FILE__) . '/install.php';
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta($create_k5n_subscribes);
        dbDelta($create_k5n_subscribes_group);

        add_option('$wp_k5n_db_version', WP_K5N_VERSION);

        // Delete notification new wp_version option
        delete_option('wp_notification_new_wp_version');
    }

    /**
     * Adding new capability in the plugin
     *
     * @param  Not param
     */
    public function add_cap() {
        // get administrator role
        $role = get_role('administrator');

        $role->add_cap('wpk5n_setting');
    }

    /**
     * Includes plugin files
     *
     * @param  Not param
     */
    public function includes() {
        $files = array(
            'includes/class-wp-k5n-settings',
            'includes/class-wp-k5n-version',
            'includes/class-wp-k5n-features',
        );

        foreach ($files as $file) {
            include_once dirname(__FILE__) . '/' . $file . '.php';
        }
    }

    /**
     * Initial plugin
     *
     * @param  Not param
     */
    private function init() {

        // Check exists require function
        if (!function_exists('wp_get_current_user')) {
            include( ABSPATH . "wp-includes/pluggable.php" );
        }

        // Add plugin caps to admin role
        if (is_admin() and is_super_admin()) {
            $this->add_cap();
        }
    }

    /**
     * Include admin assets
     *
     * @param  Not param
     */
    public function admin_assets() {
        wp_register_style('wpk5n-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', true, '1.3');
        wp_enqueue_style('wpk5n-admin-css');
    }

    /**
     * Include front table
     *
     * @param  Not param
     */
    public function front_assets() {
        
    }

    /**
     * Administrator admin_menu
     *
     * @param  Not param
     */
    public function admin_menu() {
        add_menu_page(__('K5N', 'wp-k5n'), __('K5N', 'wp-sms'), 'wpk5n_setting', 'wp-k5n', array(
            &$this->setting_page,
            'render_settings'
                ), 'dashicons-groups');

//        add_submenu_page('wp-k5n', __('Ustawienia', 'wp-k5n'), __('Ustawienia', 'wp-k5n'), 'wpk5n_setting', 'wp-k5n-settings', array(
//            &$this->setting_page,
//            'render_settings'
//        ));
    }

    /**
     * Subscribe groups admin page
     *
     * @param  Not param
     */
    public function groups_page() {

        if (isset($_GET['action'])) {
            // Add group page
            if ($_GET['action'] == 'add') {
                include_once dirname(__FILE__) . "/includes/templates/subscribe/add-group.php";
                if (isset($_POST['wp_add_group'])) {
                    $result = $this->subscribe->add_group($_POST['wp_group_name']);
                    echo $this->notice_result($result['result'], $result['message']);
                }

                return;
            }

            // Manage group page
            if ($_GET['action'] == 'edit') {
                if (isset($_POST['wp_update_group'])) {
                    $result = $this->subscribe->update_group($_GET['ID'], $_POST['wp_group_name']);
                    echo $this->notice_result($result['result'], $result['message']);
                }

                $get_group = $this->subscribe->get_group($_GET['ID']);
                include_once dirname(__FILE__) . "/includes/templates/subscribe/edit-group.php";

                return;
            }
        }

        include_once dirname(__FILE__) . '/includes/class-wp-k5n-groups-table.php';

        //Create an instance of our package class...
        $list_table = new WP_SMS_Subscribers_Groups_List_Table();

        //Fetch, prepare, sort, and filter our data...
        $list_table->prepare_items();

        include_once dirname(__FILE__) . "/includes/templates/subscribe/groups.php";
    }

}
