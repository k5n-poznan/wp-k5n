<?php

class basic_k5n_message extends WP_K5N_Message {

    public $flash = "enable";
    public $isflash = false;

    public function __construct() {
        parent::__construct();
    }

    public function send() {

        $this->to = apply_filters('wp_k5n_message_to', $this->to);

        $this->msg = apply_filters('wp_k5n_message_msg', $this->msg);

        $to = implode($this->to, ",");
        $msg = urlencode($this->msg);

        $this->insertToDB($this->msg, $this->to, $this->isflash, 0);

        do_action('wp_k5n_message_send', $this);

        return $result;
    }

}
