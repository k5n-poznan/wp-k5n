<?php

/**
 * WP SMS RestApi class
 *
 * @category   class
 * @package    WP_K5N
 * @version    1.0
 */
class WP_K5N_RestApi {

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
     * Name space
     * @var string
     */
    private $namespace;

    public function __construct() {
        global $wpk5n_option, $wpdb, $table_prefix;

        $this->options = $wpk5n_option;
        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;
        $this->namespace = 'wp-msg';
        $this->subscriptions = new WP_K5N_Subscriptions();

        if (isset($this->options['rest_api_status'])) {
            $this->init();
            add_action('rest_api_init', array(&$this, 'register_routes'));
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
    }

    /**
     * Adding new capability in the plugin
     *
     * @param  Not param
     */
    public function add_cap() {
        global $wp_roles;
        $role = get_role('administrator');

        $role->add_cap('wpk5n_res_subscribers');
    }

    public function register_routes() {
        global $wp_rest_auth_cookie;
        
        $rootpath = $this->namespace . '/v1';

        if (is_super_admin()) {
            //$wp_rest_auth_cookie = false;
        }
        
        // Add plugin rest caps to admin role
        if (is_super_admin()) {
            $this->add_cap();
        }

        register_rest_route($rootpath, '/subscriber/add', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array(&$this, 'add_subscriber'),
            'args' => array(
                'name' => array(
                    'required' => true,
                ),
                'surname' => array(
                    'required' => true,
                ),
                'mobile' => array(
                    'required' => true,
                ),
                'group_id' => array(
                    'required' => false,
                ),
            ),
        ));
        register_rest_route($rootpath, '/subscribers', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array(&$this, 'read_subscribes'),
            'args' => array(
                'group_id' => array(
                    'required' => false,
                ),
            ),
            'permission_callback' => array($this, 'rest_permissions_check'),
        ));
        register_rest_route($rootpath, '/groups', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array(&$this, 'read_groups'),
            'args' => array(
                'group_id' => array(
                    'required' => false,
                ),
            ),
            'permission_callback' => array($this, 'rest_permissions_check'),
        ));
    }

    public function add_subscriber(WP_REST_Request $request) {
        //get parameters from request
        $params = $request->get_params();

        $data = $this->subscriptions->add_subscriber($params['name'], $params['surname'], $params['mobile'], $params['group_id']);

        if ($data) {
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('subscriber', __('Could not be added', 'wp-k5n'));
        }
    }

    public function read_subscribes(WP_REST_Request $request) {
        global $wpdb, $table_prefix;

        //get parameters from request
        $params = $request->get_params();

        $result = $wpdb->get_results("SELECT * FROM `{$table_prefix}k5n_subscribes`", ARRAY_A);

        if ($result) {
            return new WP_REST_Response($result, 200);
        } else {
            return new WP_Error('subscriber', __('Could not read subsribers', 'wp-k5n'));
        }
    }

    public function read_groups(WP_REST_Request $request) {
        global $wpdb, $table_prefix;

        //get parameters from request
        $params = $request->get_params();

        $result = $wpdb->get_results( "SELECT * FROM `{$table_prefix}k5n_subscribes_group`", ARRAY_A );

        if ($result) {
            return new WP_REST_Response($result, 200);
        } else {
            return new WP_Error('subscriber', __('Could not read groups', 'wp-k5n'));
        }
    }

    public function rest_permissions_check($request) {

        if (!current_user_can('wpk5n_res_subscribers')) {
            return new WP_Error('rest_cannot_subscribers', __('Sorry, you are not allowed subscribers function.'), array('status' => rest_authorization_required_code()));
        }

        return true;
    }

}

new WP_K5N_RestApi();
