<?php

/**
 * WP K5N version class
 *
 * @category   class
 * @package    WP_K5N
 */
class WP_K5N_Version {

    public $options;

    /**
     * WP_SMS_Version constructor.
     */
    public function __construct() {
        global $wpk5n_option;
        $this->options = $wpk5n_option;

    }



}

new WP_K5N_Version();
