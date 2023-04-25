<?php

class Ecwd_mail {

    private $to;
    private $headers = array();
    private $attachments = array();
    private $subject = "";
    private $placeHolders = array();
    private $values = array();
    private $mail_content = "";

    public function __construct($from = '', $to = '') {
        if ($from != '') {
            $this->set_from($from);
        }
        if ($to != '') {
            $this->set_to($to);
        }
    }

    public function replace($option, $html = "") {
        if ($html == "" || $html == null) {
            $content = get_option($option);
            if ($content == false) {
                do_action('ecwd_reset_template', $option);
            }
            $content = stripslashes(get_option($option));
        } else {
            $content = $html;
        }
        $this->headers[] = 'Content-Type: text/html; charset=UTF-8';
        $this->mail_content = $content;
        $this->hide_empty_placeholders();
        $this->mail_content = preg_replace_callback('/({ecwd_[^}]*})/i', array($this, 'replace_one'), $this->mail_content);
		$this->mail_content = strip_shortcodes($this->mail_content);
    }

    private function hide_empty_placeholders() {
        foreach ($this->values as $key => $value) {
            if (!$value || $value == '' || $value == null) {
                $delimiter = '<!-- start_' . $key . ' -->';
                $text = explode($delimiter, $this->mail_content);
                $new_content = "";
                if (count($text) == 2) {
                    $new_content = $text[0];
                    $delimiter2 = '<!-- end_' . $key . ' -->';
                    $text = explode($delimiter2, $text[1]);
                    if (count($text) == 2) {
                        $new_content .= $text[1];
                        $this->mail_content = $new_content;
                    }
                }
            }
        }
    }

    public function replace_one($matches) {
        $key = str_replace(array('{', '}'), '', $matches[0]);
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return '';
    }

    public function set_to($to) {
        $this->to = $to;
    }

    public function set_from($from_name, $from_mail = null) {
        if ($from_mail != null) {
            $from = "From: " . $from_name;
            $from .= " < " . $from_mail . " >";
            $this->headers[] = $from;
        }
    }

    public function set_subject($subject) {
        $this->subject = $subject;
    }

    public function set_headers($header = array()) {
        $this->headers = $header;
    }

    public function add_header($header) {
        $this->headers[] = $header;
    }

    public function send_email() {
        return wp_mail($this->to, $this->subject, $this->mail_content, $this->headers);
    }

    public function set_placeHolders($placeHolders = array()) {
        $this->placeHolders = $placeHolders;
    }

    public function get_placeHolders() {
        return $this->placeHolders;
    }

    public function set_values($values = array()) {
        $this->values = $values;
    }

    public function get_values() {
        return $this->values;
    }

    public function get_mail_content() {
        return $this->mail_content;
    }

    public function set_mail_content($msg) {
        $this->mail_content = $msg;
    }

}
