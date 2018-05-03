<?php

/**
 * @category   class
 * @package    WP_K5N
 * @version    1.1
 */
class WP_K5N_Subscriptions {

    /**
     * Wordpress Dates
     *
     * @var string
     */
    public $date;

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
     * Constructors
     */
    public function __construct() {
        global $wpdb, $table_prefix;

        $this->date = WP_SMS_CURRENT_DATE;
        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;
    }

    /**
     * Add Subscriber
     *
     * @param $name
     * @param $mobile
     * @param string $group_id
     * @param string $status
     * @param $key
     *
     * @return array
     * @internal param param $Not
     */
    public function add_subscriber($name, $surname, $mobile, $group_id = '', $status = '1', $key = nul) {
        if ($this->is_duplicate($mobile, $group_id)) {
            return array('result' => 'error',
                'message' => __('Taki sam numer telefonu należy do innego subskrybenta.', 'wp-k5n')
            );
        }

        $result = $this->db->insert(
                $this->tb_prefix . "k5n_subscribes", array(
            'date' => $this->date,
            'name' => $name,
            'surname' => $surname,
            'mobile' => $mobile,
            'status' => $status,
            'activate_key' => $key,
            'group_ID' => $group_id,
                )
        );

        if ($result) {
            /**
             * Run hook after adding subscribe.
             *
             * @since 3.0
             *
             * @param string $name name.
             * @param string $mobile mobile.
             */
            do_action('wp_k5n_add_subscriber', $name, $mobile);

            return array('result' => 'update', 'message' => __('Dodano subskrybenta.', 'wp-k5n'));
        }
    }

    /**
     * Get Subscriber
     *
     * @param  Not param
     *
     * @return array|null|object|void
     */
    public function get_subscriber($id) {
        $result = $this->db->get_row("SELECT * FROM `{$this->tb_prefix}k5n_subscribes` WHERE ID = '" . $id . "'");

        if ($result) {
            return $result;
        }
    }

    /**
     * Delete Subscriber
     *
     * @param  Not param
     *
     * @return false|int|void
     */
    public function delete_subscriber($id) {
        $result = $this->db->delete(
                $this->tb_prefix . "k5n_subscribes", array(
            'ID' => $id,
                )
        );

        if ($result) {
            /**
             * Run hook after deleting subscribe.
             *
             * @since 3.0
             *
             * @param string $result result query.
             */
            do_action('wp_k5n_delete_subscriber', $result);

            return $result;
        }
    }

    /**
     * Delete subscribers by number
     *
     * @param $mobile
     * @param null $group_id
     *
     * @return array
     */
    public function delete_subscriber_by_number($mobile, $group_id = null) {
        $result = $this->db->delete(
                $this->tb_prefix . "k5n_subscribes", array(
            'mobile' => $mobile,
            'group_id' => $group_id,
                )
        );

        if (!$result) {
            return array('result' => 'error', 'message' => __('Nie znaleziono subskrybenta.', 'wp-k5n'));
        }

        /**
         * Run hook after deleting subscribe.
         *
         * @since 3.0
         *
         * @param string $result result query.
         */
        do_action('wp_k5n_delete_subscriber', $result);

        return array('result' => 'update', 'message' => __('Usunięto subskrybenta.', 'wp-k5n'));
    }

    /**
     * Update Subscriber
     *
     * @param $id
     * @param $name
     * @param $mobile
     * @param string $group_id
     * @param string $status
     *
     * @return array|void
     * @internal param param $Not
     */
    public function update_subscriber($id, $name, $surname, $mobile, $group_id = '', $status = '1') {
        if (empty($id) or empty($name) or empty($mobile)) {
            return;
        }

        if ($this->is_duplicate($mobile, $group_id, $id)) {
            return array('result' => 'error',
                'message' => __('Taki sam numer telefonu należy do innego subskrybenta.', 'wp-k5n')
            );
        }

        $result = $this->db->update(
                $this->tb_prefix . "k5n_subscribes", array(
            'name' => $name,
            'surname' => $surname,
            'mobile' => $mobile,
            'group_ID' => $group_id,
            'status' => $status,
                ), array(
            'ID' => $id
                )
        );

        if ($result) {

            /**
             * Run hook after updating subscribe.
             *
             * @since 3.0
             *
             * @param string $result result query.
             */
            do_action('wp_k5n_update_subscriber', $result);

            return array('result' => 'update', 'message' => __('Zmieniono dane subskrybanta.', 'wp-k5n'));
        }
    }

    /**
     * Get Subscriber
     *
     * @param  Not param
     *
     * @return array|null|object
     */
    public function get_groups() {
        $result = $this->db->get_results("SELECT * FROM `{$this->tb_prefix}k5n_subscribes_group`");

        if ($result) {
            return $result;
        }
    }

    /**
     * Get Group
     *
     * @param  Not param
     *
     * @return array|null|object|void
     */
    public function get_group($group_id) {
        $result = $this->db->get_row("SELECT * FROM `{$this->tb_prefix}k5n_subscribes_group` WHERE ID = '" . $group_id . "'");

        if ($result) {
            return $result;
        }
    }

    /**
     * Add Group
     *
     * @param  Not param
     *
     * @return array
     */
    public function add_group($name) {
        if (empty($name)) {
            return array('result' => 'error', 'message' => __('Pole nazwy jest puste!', 'wp-k5n'));
        }

        $result = $this->db->insert(
                $this->tb_prefix . "k5n_subscribes_group", array(
            'name' => $name,
                )
        );

        if ($result) {

            /**
             * Run hook after adding group.
             *
             * @since 3.0
             *
             * @param string $result result query.
             */
            do_action('wp_k5n_add_group', $result);

            return array('result' => 'update', 'message' => __('Dodano nową grupę.', 'wp-k5n'));
        }
    }

    /**
     * Delete Group
     *
     * @param  Not param
     *
     * @return false|int|void
     */
    public function delete_group($id) {

        if (empty($id)) {
            return;
        }

        $result = $this->db->delete(
                $this->tb_prefix . "k5n_subscribes_group", array(
            'ID' => $id,
                )
        );

        if ($result) {

            /**
             * Run hook after deleting group.
             *
             * @since 3.0
             *
             * @param string $result result query.
             */
            do_action('wp_k5n_delete_group', $result);

            return $result;
        }
    }

    /**
     * Update Group
     *
     * @param $id
     * @param $name
     *
     * @return array|void
     * @internal param param $Not
     */
    public function update_group($id, $name) {
        if (empty($id) or empty($name)) {
            return;
        }

        $result = $this->db->update(
                $this->tb_prefix . "k5n_subscribes_group", array(
            'name' => $name,
                ), array(
            'ID' => $id
                )
        );

        if ($result) {

            /**
             * Run hook after updating group.
             *
             * @since 3.0
             *
             * @param string $result result query.
             */
            do_action('wp_k5n_update_group', $result);

            return array('result' => 'update', 'message' => __('Zmieniono dane grupy.', 'wp-k5n'));
        }
    }

    /**
     * Check the mobile number is duplicate
     *
     * @param $mobile_number
     * @param null $group_id
     * @param null $id
     *
     * @return array|null|object|void
     */
    private function is_duplicate($mobile_number, $group_id = null, $id = null) {
        $sql = "SELECT * FROM `{$this->tb_prefix}k5n_subscribes` WHERE mobile = '" . $mobile_number . "'";

        if ($group_id) {
            $sql .= " AND group_id = '" . $group_id . "'";
        }

        if ($id) {
            $sql .= " AND id != '" . $id . "'";
        }

        $result = $this->db->get_row($sql);

        return $result;
    }

}
