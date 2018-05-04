<?php

class Default_Message extends WP_K5N_Message {

    private $wsdl_link = '';
    public $tariff = '';
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;
    public $bulk_send = false;

    public function __construct() {
        $this->validateNumber = "1xxxxxxxxxx";
    }

    public function send() {
        // Check gateway credit
        return new WP_Error('send-k5n', __('Nie zdefiniowano producenta komunikatów k5N', 'wp-k5n'));
    }


}
