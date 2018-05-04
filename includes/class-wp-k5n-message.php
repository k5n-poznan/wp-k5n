<?php

/**
 * @category   class
 * @package    WP_K5N_Message
 * @version    1.0
 */
abstract class WP_K5N_Message {

    /**
     * Send SMS to number
     *
     * @var string
     */
    public $to;

    /**
     * SMS text
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
     * Plugin options
     *
     * @var string
     */
    protected $options;

    /**
     * Constructors
     */
    public function __construct() {
        global $wpdb, $table_prefix, $wpk5n_option;

        $this->db = $wpdb;
        $this->tb_prefix = $table_prefix;
        $this->options = $wpk5n_option;
    }

    public function insertToDB($sender, $message, $recipient, $flash, $status = '0') {
        return $this->db->insert(
                        $this->tb_prefix . "sms_send", array(
                    'date' => WP_K5N_CURRENT_DATE,
                    'message' => $message,
                    'status' => $status,
                    'flash' => $flash,
                    'recipient' => implode(',', $recipient)
                        )
        );
    }

    /**
     * Apply Country code to prefix numbers
     *
     * @param $recipients
     *
     * @return array
     */
    public function applyCountryCode($recipients = array()) {
        $country_code = $this->options['mobile_county_code'];
        $numbers = array();

        foreach ($recipients as $number) {
            // Remove zero from first number
            $number = ltrim($number, '0');

            // Add country code to prefix number
            $numbers[] = $country_code . $number;
        }

        return $numbers;
    }

}
