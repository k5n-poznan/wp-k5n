<?php

/**
 * WP K5N notifications class
 *
 * @category   class
 * @package    WP_K5N
 * @version    1.0
 */
class WP_K5N_Notifications {

    public $msg;
    public $date;
    public $options;

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
     * WP_K5N_Notifications constructor.
     */
    public function __construct() {
        global $wpk5n_option, $k5nmsg, $wp_version, $wpdb, $table_prefix;

        $this->msg = $k5nmsg;
        $this->date = WP_K5N_CURRENT_DATE;
        $this->options = $wpk5n_option;
        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;

        if (isset($this->options['notif_publish_new_post'])) {
            add_action('add_meta_boxes', array($this, 'notification_meta_box'));
            add_action('publish_post', array($this, 'new_post'), 10, 2);
        }

    }

    public function notification_meta_box() {
        add_meta_box('subscribe-meta-box', __('Powiadomienie do K5N', 'wp-sms'), array(
            $this,
            'notification_meta_box_handler'
                ), 'post', 'normal', 'high');
    }

    /**
     * @param $post
     */
    public function notification_meta_box_handler($post) {
        global $wpdb, $table_prefix;

        $get_group_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}k5n_subscribes_group`");
        $username_active = $wpdb->query("SELECT * FROM {$table_prefix}k5n_subscribes WHERE status = '1'");
        include_once dirname(__FILE__) . "/templates/wp-k5n-meta-box.php";
    }

    /**
     * @param $ID
     * @param $post
     *
     * @return null
     * @internal param $post_id
     */
    public function new_post($ID, $post) {
        if ($_REQUEST['wps_send_subscribe'] == 'yes') {
            if ($_REQUEST['wps_subscribe_group'] == 'all') {
                $this->msg->to = $this->db->get_col("SELECT mobile FROM {$this->tb_prefix}k5n_subscribes");
            } else {
                $this->msg->to = $this->db->get_col("SELECT mobile FROM {$this->tb_prefix}k5n_subscribes WHERE group_ID = '{$_REQUEST['wps_subscribe_group']}'");
            }

            $template_vars = array(
                '%post_title%' => get_the_title($ID),
                '%post_content%' => wp_trim_words($post->post_content, 10),
                '%post_url%' => wp_get_shortlink($ID),
                '%post_date%' => get_post_time('Y-m-d', true, $ID, true),
            );

            $message = str_replace(array_keys($template_vars), array_values($template_vars), $_REQUEST['wpk5n_text_template']);

            $this->msg->msg = $message;
            $this->msg->send();
        }
    }

}

new WP_K5N_Notifications();
