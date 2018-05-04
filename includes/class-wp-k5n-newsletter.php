<?php

/**
 * WP SMS newsletter class
 *
 * @category   class
 * @package    WP_K5N
 * @version    1.0
 */
class WP_K5N_Newsletter {

    /**
     * Options
     *
     * @var string
     */
    protected $option;

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
     * WP SMS subscribe object
     *
     * @var string
     */
    public $subscribe;

    public function __construct() {
        global $wpk5n_option, $wpdb, $table_prefix;

        $this->options = $wpk5n_option;
        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;
        $this->subscribe = new WP_K5N_Subscriptions();

        // Load scripts
        add_action('wp_enqueue_scripts', array(&$this, 'load_script'));

        // Subscribe ajax action
        add_action('wp_ajax_subscribe_ajax_action', array(&$this, 'subscribe_ajax_action_handler'));
        add_action('wp_ajax_nopriv_subscribe_ajax_action', array(&$this, 'subscribe_ajax_action_handler'));

        // Subscribe activation action
        add_action('wp_ajax_activation_ajax_action', array(&$this, 'activation_ajax_action_handler'));
        add_action('wp_ajax_nopriv_activation_ajax_action', array(&$this, 'activation_ajax_action_handler'));
    }

    /**
     * Include front table
     *
     * @param  Not param
     */
    public function load_script() {
        // jQuery will be included automatically
        wp_enqueue_script('ajax-script', WP_K5N_DIR_PLUGIN . 'assets/js/script.js', array('jquery'), 1.0);

        // Ajax params
        wp_localize_script('ajax-script', 'ajax_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpk5n-nonce')
        ));
    }

    /**
     * Subscribe ajax handler
     */
    public function subscribe_ajax_action_handler() {
        // Check nonce
        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce($nonce, 'wpk5n-nonce')) {
            // Stop executing script
            die('Busted!');
        }

        // Get widget option
        $get_widget = get_option('widget_wpk5n_subscribe_widget');
        $widget_options = $get_widget[$_POST['widget_id']];

        // Check current widget
        if (!isset($widget_options)) {
            // Return response
            echo json_encode(array('status' => 'error',
                'response' => __('Brak parametrów! Proszę odświerzyć bieżącą stronę!', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }

        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $mobile = trim($_POST['mobile']);
        $group = trim($_POST['group']);
        $type = $_POST['type'];

        if (!$name or ! $surname or ! $mobile) {
            // Return response
            echo json_encode(array('status' => 'error',
                'response' => __('Proszę wypełnić wszystkie pola', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }

        if (preg_match(WP_K5N_MOBILE_REGEX, $mobile) == false) {
            // Return response
            echo json_encode(array('status' => 'error',
                'response' => __('Proszę wprowadzić prawidłowy numer telefonu', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }

        if ($widget_options['mobile_number_terms']) {
            if ($widget_options['mobile_field_max']) {
                if (strlen($mobile) > $widget_options['mobile_field_max']) {
                    // Return response
                    echo json_encode(array('status' => 'error',
                        'response' => sprintf(__('Twój numer telefonu powinien mieć poniżej %s cyfr', 'wp-k5n'), $widget_options['mobile_field_max'])
                    ));

                    // Stop executing script
                    die();
                }
            }

            if ($widget_options['mobile_field_min']) {
                if (strlen($mobile) < $widget_options['mobile_field_min']) {
                    // Return response
                    echo json_encode(array('status' => 'error',
                        'response' => sprintf(__('Twój numer telefonu powinien mieć ponad %s cyfr', 'wp-k5n'), $widget_options['mobile_field_min'])
                    ));

                    // Stop executing script
                    die();
                }
            }
        }

        if ($type == 'subscribe') {
            if ($widget_options['send_activation_code'] and $this->options['gateway_name']) {

                // Check gateway setting
                if (!$this->options['gateway_name']) {
                    // Return response
                    echo json_encode(array('status' => 'error',
                        'response' => __('Usługodawca nie jest dostępny do wysyłania klucza aktywacji do telefonu komórkowego. Skontaktuj się z administratorem strony.', 'wp-k5n')
                    ));

                    // Stop executing script
                    die();
                }

                $key = rand(1000, 9999);
//                $this->register->to = array($mobile);
//                $this->register->msg = __('Your activation code', 'wp-k5n') . ': ' . $key;
//                $this->register->SendSMS();

                // Add subscribe to database
                $result = $this->subscribe->add_subscriber($name, $mobile, $group, '0', $key);

                if ($result['result'] == 'error') {
                    // Return response
                    echo json_encode(array('status' => 'error', 'response' => $result['message']));

                    // Stop executing script
                    die();
                }

                // Return response
                echo json_encode(array('status' => 'success',
                    'response' => __('Dołączasz do newslettera, kod aktywacyjny wysyłany na telefon komórkowy.', 'wp-k5n'),
                    'action' => 'activation'
                ));

                // Stop executing script
                die();
            } else {

                // Add subscribe to database
                $result = $this->subscribe->add_subscriber($name, $mobile, $group, '1');

                if ($result['result'] == 'error') {
                    // Return response
                    echo json_encode(array('status' => 'error', 'response' => $result['message']));

                    // Stop executing script
                    die();
                }

                // Send welcome message
                if ($widget_options['send_welcome_k5n']) {
                    $template_vars = array(
                        '%subscribe_name%' => $name,
                        '%subscribe_mobile%' => $mobile,
                    );

                    $message = str_replace(array_keys($template_vars), array_values($template_vars), $widget_options['welcome_k5n_template']);

//                    $this->register->to = array($mobile);
//                    $this->register->msg = $message;
//                    $this->register->SendSMS();
                }

                // Return response
                echo json_encode(array('status' => 'success',
                    'response' => __('Wiadomości będą przysyłane na podany nr telefonu', 'wp-k5n')
                ));

                // Stop executing script
                die();
            }
        } else if ($type == 'unsubscribe') {
            // Delete subscriber
            $result = $this->subscribe->delete_subscriber_by_number($mobile, $group);

            // Check result
            if ($result['result'] == 'error') {
                // Return response
                echo json_encode(array('status' => 'error', 'response' => $result['message']));

                // Stop executing script
                die();
            }

            // Return response
            echo json_encode(array('status' => 'success',
                'response' => __('Zrezygnowałeś z wiadomości od K5N.', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }

        // Stop executing script
        die();
    }

    /**
     * Activation ajax handler
     */
    public function activation_ajax_action_handler() {
        // Check nonce
        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce($nonce, 'wpk5n-nonce')) {
            // Stop executing script
            die('Busted!');
        }

        // Get widget option
        $get_widget = get_option('widget_wpk5n_subscribe_widget');
        $widget_options = $get_widget[$_POST['widget_id']];

        // Check current widget
        if (!isset($widget_options)) {
            // Return response
            echo json_encode(array('status' => 'error',
                'response' => __('Brak parametrów! Proszę odświerzyć bieżącą stronę!', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }

        $mobile = trim($_POST['mobile']);
        $activation = trim($_POST['activation']);

        if (!$mobile) {
            // Return response
            echo json_encode(array('status' => 'error', 'response' => __('Brak numeru telefonu komórkowego!', 'wp-k5n')));

            // Stop executing script
            die();
        }

        if (!$activation) {
            // Return response
            echo json_encode(array('status' => 'error',
                'response' => __('Wprowadź kod aktywacyjny!', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }

        $check_mobile = $this->db->get_row($this->db->prepare("SELECT * FROM `{$this->tb_prefix}k5n_subscribes` WHERE `mobile` = '%s'", $mobile));

        if ($activation != $check_mobile->activate_key) {
            // Return response
            echo json_encode(array('status' => 'error', 'response' => __('Kod aktywacyjny jest nieprawidłowy!', 'wp-k5n')));

            // Stop executing script
            die();
        }

        $result = $this->db->update("{$this->tb_prefix}k5n_subscribes", array('status' => '1'), array('mobile' => $mobile));

        if ($result) {
            // Return response
            echo json_encode(array('status' => 'success',
                'response' => __('Subskrypcja w K5N została przyjęta!', 'wp-k5n')
            ));

            // Stop executing script
            die();
        }
    }

}

new WP_K5N_Newsletter();
