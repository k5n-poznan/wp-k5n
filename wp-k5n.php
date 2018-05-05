<?php

/**
 * Plugin Name: WP K5N
 * Plugin URI: http://k5n.pl/
 * Description: Ta wtyczka instaluje dodatkowe funkcje dla WordPress wykorzystywane przez społecznośc kościoła K5N
 * Version: 1.0.1
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

include_once dirname(__FILE__) . '/includes/functions.php';
$k5nmsg = initial_message_producer();

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
     * WP message object
     *
     * @var string
     */
    public $msg;

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
        global $wpdb, $table_prefix, $wpk5n_option, $k5nmsg;

        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;
        $this->options = $wpk5n_option;

        // Load text domain
        add_action('init', array($this, 'load_textdomain'));

        __('WP K5N', 'wp-k5n');
        __('A simple and powerful texting plugin for wordpress', 'wp-k5n');

        $this->includes();
        $this->msg = $k5nmsg;

        $this->setting_page = new WP_K5N_Settings();


        $this->init();
        $this->subscribe = new WP_K5N_Subscriptions();



        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'front_assets'));

        add_action('admin_bar_menu', array($this, 'adminbar'));
        add_action('dashboard_glance_items', array($this, 'dashboard_glance'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_filter('plugin_row_meta', array($this, 'meta_links'), 0, 2);

        add_action('widgets_init', array($this, 'register_widget'));

        add_filter('wp_k5n_message_to', array($this, 'modify_to_send'));
    }

    public function meta_links($links, $file) {
        if ($file == 'wp-k5n/wp-k5n.php') {
//            $rate_url = 'http://wordpress.org/support/view/plugin-reviews/wp-k5n?rate=5#postform';
//            $links[] = '<a href="' . $rate_url . '" target="_blank" class="wpk5n-plugin-meta-link" title="' . __('Click here to rate and review this plugin on WordPress.org', 'wp-k5n') . '">' . __('Rate this plugin', 'wp-k5n') . '</a>';
//            $newsletter_url = WP_K5N_SITE . '/newsletter';
//            $links[] = '<a href="' . $newsletter_url . '" target="_blank" class="wpk5n-plugin-meta-link" title="' . __('Click here to rate and review this plugin on WordPress.org', 'wp-k5n') . '">' . __('Subscribe to our Phone Newsletter', 'wp-k5n') . '</a>';
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
        dbDelta($create_k5n_outbox);

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

        $role->add_cap('wpk5n_sendsms');
        $role->add_cap('wpk5n_outbox');
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
            'includes/class-wp-k5n-notifications',
            'includes/class-wp-k5n-features',
            'includes/class-wp-k5n-subscribers',
            'includes/class-wp-k5n-newsletter',
            'includes/class-wp-k5n-subscribe-widget',
            'includes/class-wp-k5n-rest-api',
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

        wp_enqueue_style('wpk5n-chosen-css', plugin_dir_url(__FILE__) . 'assets/css/chosen.min.css', true, '1.2.0');
        wp_enqueue_script('wpk5n-chosen-js', plugin_dir_url(__FILE__) . 'assets/js/chosen.jquery.min.js', true, '1.2.0');
        wp_enqueue_script('wpk5n-word-and-character-counter-js', plugin_dir_url(__FILE__) . 'assets/js/jquery.word-and-character-counter.min.js', true, '2.5.0');
        wp_enqueue_script('wpk5n-admin-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', true, '1.2.0');
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
     * Admin bar plugin
     *
     * @param  Not param
     */
    public function adminbar() {
        global $wp_admin_bar, $wpk5n_option;

        if (is_super_admin() && is_admin_bar_showing()) {
            $wp_admin_bar->add_menu(array(
                'id' => 'wp-send-sms',
                'parent' => 'new-content',
                'title' => __('Powiadomienie K5N', 'wp-k5n'),
                'href' => $this->admin_url . '/admin.php?page=wp-k5n-sendsms'
            ));
        }
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

        add_submenu_page('wp-k5n', __('Ustawienia', 'wp-k5n'), __('Ustawienia', 'wp-k5n'), 'wpk5n_setting', 'wp-k5n', array(
            &$this->setting_page,
            'render_settings'));


        add_submenu_page('wp-k5n', __('Wyślij powiadomienie', 'wp-k5n'), __('Wyślij powiadomienie', 'wp-k5n'), 'wpk5n_sendsms', 'wp-k5n-sendsms', array(
            $this,
            'send_page'
        ));

        add_submenu_page('wp-k5n', __('Powiadomienia K5N', 'wp-sms'), __('Powiadomienia K5N', 'wp-k5n'), 'wpk5n_outbox', 'wp-k5n-outbox', array(
            $this,
            'outbox_page'
        ));


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
     * Register widget
     */
    public function register_widget() {
        register_widget('WPK5N_Subscribe_Widget');
    }

    /**
     * Modify destination number
     *
     * @param  array $to
     *
     * @return array/string
     */
    public function modify_to_send($to) {
        return $to;
    }

    /**
     * Sending sms admin page
     *
     * @param  Not param
     */
    public function send_page() {
        global $wpsms_option;

        $get_group_result = $this->db->get_results("SELECT * FROM `{$this->tb_prefix}k5n_subscribes_group`");
        $get_users_mobile = $this->db->get_col("SELECT `meta_value` FROM `{$this->tb_prefix}usermeta` WHERE `meta_key` = 'mobile'");

        if (isset($_POST['SendMessage'])) {
            if ($_POST['wp_get_message']) {
                if ($_POST['wp_send_to'] == "wp_subscribe_username") {
                    if ($_POST['wpk5n_group_name'] == 'all') {
                        $this->msg->to = $this->db->get_col("SELECT mobile FROM {$this->tb_prefix}k5n_subscribes WHERE `status` = '1'");
                    } else {
                        $this->msg->to = $this->db->get_col("SELECT mobile FROM {$this->tb_prefix}k5n_subscribes WHERE `status` = '1' AND `group_ID` = '" . $_POST['wpsms_group_name'] . "'");
                    }
                } else if ($_POST['wp_send_to'] == "wp_users") {
                    $this->msg->to = $get_users_mobile;
                } else if ($_POST['wp_send_to'] == "wp_tellephone") {
                    $this->msg->to = explode(",", $_POST['wp_get_number']);
                }

                $this->msg->msg = $_POST['wp_get_message'];

                if (isset($_POST['wp_flash'])) {
                    $this->msg->isflash = true;
                } else {
                    $this->msg->isflash = false;
                }

                // Send sms
                $response = $this->msg->send();

                if (is_wp_error($response)) {
                    if (is_array($response->get_error_message())) {
                        $response = print_r($response->get_error_message(), 1);
                    } else {
                        $response = $response->get_error_message();
                    }

                    echo "<div class='error'><p>" . sprintf(__('<strong>Komunikat nie został dostarczony! Otrzymane wyniki:</strong> %s', 'wp-k5n'), $response) . "</p></div>";
                } else {
                    echo "<div class='updated'><p>" . __('Komunikat został dodany i oczekuje na wysłanie', 'wp-sms') . "</p></div>";
                }
            } else {
                echo "<div class='error'><p>" . __('Proszę wprowadzić wiadomość', 'wp-sms') . "</p></div>";
            }
        }

        include_once dirname(__FILE__) . "/includes/templates/outbox/send-sms.php";
    }

    /**
     * Outbox messages admin page
     *
     * @param  Not param
     */
    public function outbox_page() {
        include_once dirname(__FILE__) . '/includes/class-wp-k5n-outbox.php';

        //Create an instance of our package class...
        $list_table = new WP_K5N_Outbox_List_Table();

        //Fetch, prepare, sort, and filter our data...
        $list_table->prepare_items();

        include_once dirname(__FILE__) . "/includes/templates/outbox/outbox.php";
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

    /**
     * Show message notice in admin
     *
     * @param $result
     * @param $message
     *
     * @return string|void
     * @internal param param $Not
     */
    public function notice_result($result, $message) {
        if (empty($result)) {
            return;
        }

        if ($result == 'error') {
            return '<div class="updated settings-error notice error is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __('Zamknij', 'wp-k5n') . '</span></button></div>';
        }

        if ($result == 'update') {
            return '<div class="updated settings-update notice is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __('Zamknij', 'wp-k5n') . '</span></button></div>';
        }
    }

}
