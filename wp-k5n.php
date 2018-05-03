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

include_once dirname( __FILE__ ) . '/includes/functions.php';

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

    /**
     * WP K5N subscribe object
     *
     * @var string
     */
    public $subscribe;

    /**
     * Settings
     *
     * @var string
     */
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
        $this->subscribe = new WP_K5N_Subscriptions();



        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'front_assets'));

        add_action('dashboard_glance_items', array($this, 'dashboard_glance'));
        add_filter('plugin_row_meta', array($this, 'meta_links'), 0, 2);

        add_action('admin_menu', array($this, 'admin_menu'));
    }

    public function meta_links($links, $file) {
        if ($file == 'wp-k5n/wp-k5n.php') {
            $rate_url = 'http://wordpress.org/support/view/plugin-reviews/wp-k5n?rate=5#postform';
            $links[] = '<a href="' . $rate_url . '" target="_blank" class="wpk5n-plugin-meta-link" title="' . __('Click here to rate and review this plugin on WordPress.org', 'wp-k5n') . '">' . __('Rate this plugin', 'wp-k5n') . '</a>';

            $newsletter_url = WP_K5N_SITE . '/newsletter';
            $links[] = '<a href="' . $newsletter_url . '" target="_blank" class="wpk5n-plugin-meta-link" title="' . __('Click here to rate and review this plugin on WordPress.org', 'wp-k5n') . '">' . __('Subscribe to our Phone Newsletter', 'wp-k5n') . '</a>';
        }

        return $links;
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
        $role->add_cap('wpk5n_subscribers');
        $role->add_cap('wpk5n_subscribe_groups');
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
            'includes/class-wp-k5n-subscribers',
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
        wp_register_style('wpk5n-subscribe', plugin_dir_url(__FILE__) . 'assets/css/subscribe.css', true, '1.1');
        wp_enqueue_style('wpk5n-subscribe');
    }

    /**
     * Dashboard glance plugin
     *
     * @param  Not param
     */
    public function dashboard_glance() {
        $subscribe = $this->db->get_var("SELECT COUNT(*) FROM {$this->tb_prefix}k5n_subscribes");
        echo "<li class='wpk5n-subscribe-count'><a href='" . $this->admin_url . "admin.php?page=wp-k5n-subscribers'>" . sprintf(__('%s Subskrybentów K5N', 'wp-k5n'), $subscribe) . "</a></li>";
    }

    /**
     * Administrator admin_menu
     *
     * @param  Not param
     */
    public function admin_menu() {
        add_menu_page(__('K5N', 'wp-k5n'), __('K5N', 'wp-k5n'), 'wpk5n_setting', 'wp-k5n', array(
            &$this->setting_page,
            'render_settings'
                ), 'dashicons-groups');

        add_submenu_page('wp-k5n', __('Subskrybenci', 'wp-k5n'), __('Subskrybenci', 'wp-k5n'), 'wpk5n_subscribers', 'wp-k5n-subscribers', array(
            $this,
            'subscribe_page'
        ));

        add_submenu_page('wp-k5n', __('Grupy subskrybentów', 'wp-k5n'), __('Grupy subskrybentów', 'wp-k5n'), 'wpk5n_subscribe_groups', 'wp-k5n-subscribers-group', array(
            $this,
            'groups_page'
        ));
    }

    /**
     * Subscribe admin page
     *
     * @param  Not param
     */
    public function subscribe_page() {

        if (isset($_GET['action'])) {
            // Add subscriber page
            if ($_GET['action'] == 'add') {
                include_once dirname(__FILE__) . "/includes/templates/subscribe/add-subscriber.php";

                if (isset($_POST['wp_add_subscribe'])) {
                    $result = $this->subscribe->add_subscriber($_POST['wp_subscribe_name'], $_POST['wp_subscribe_surname'], $_POST['wp_subscribe_mobile'], $_POST['wpk5n_group_name']);
                    echo $this->notice_result($result['result'], $result['message']);
                }

                return;
            }

            // Edit subscriber page
            if ($_GET['action'] == 'edit') {
                if (isset($_POST['wp_update_subscribe'])) {
                    $result = $this->subscribe->update_subscriber($_GET['ID'], $_POST['wp_subscribe_name'], $_POST['wp_subscribe_surname'], $_POST['wp_subscribe_mobile'], $_POST['wpk5n_group_name'], $_POST['wpk5n_subscribe_status']);
                    echo $this->notice_result($result['result'], $result['message']);
                }

                $get_subscribe = $this->subscribe->get_subscriber($_GET['ID']);
                include_once dirname(__FILE__) . "/includes/templates/subscribe/edit-subscriber.php";

                return;
            }

            // Import subscriber page
            if ($_GET['action'] == 'import') {
                include_once dirname(__FILE__) . "/import.php";
                include_once dirname(__FILE__) . "/includes/templates/subscribe/import.php";

                return;
            }

            // Export subscriber page
            if ($_GET['action'] == 'export') {
                include_once dirname(__FILE__) . "/includes/templates/subscribe/export.php";

                return;
            }
        }

        include_once dirname(__FILE__) . '/includes/class-wp-k5n-subscribers-table.php';

        //Create an instance of our package class...
        $list_table = new WP_K5N_Subscribers_List_Table();

        //Fetch, prepare, sort, and filter our data...
        $list_table->prepare_items();

        include_once dirname(__FILE__) . "/includes/templates/subscribe/subscribes.php";
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
        $list_table = new WP_K5N_Subscribers_Groups_List_Table();

        //Fetch, prepare, sort, and filter our data...
        $list_table->prepare_items();

        include_once dirname(__FILE__) . "/includes/templates/subscribe/groups.php";
    }

}
