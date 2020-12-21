<?php

namespace narad1972\TwitterClient;

class UserCredentials {
    public $name;
    public $email;
    public $access_token;
    public $access_token_secret;

    public function __construct($user_name = null, $user_email = null, $user_access_token = null, $user_access_token_secret = null)
    {
        $this->name = $user_name;
        $this->email = $user_email;
        $this->access_token = $user_access_token;
        $this->access_token_secret = $user_access_token_secret;
    }
    public function from_array($credentials) {
        $this->name = $credentials['name'];
        $this->email = $credentials['email'];
        $this->access_token = $credentials['access_token'];
        $this->access_token_secret = $credentials['access_token_secret'];
    }

    public function from_json($json_cred) {
        $cred_array = json_decode($json_cred, true);
        $this->from_array($cred_array);
    }

    public function from_file($cred_file) {
        $json = file_get_contents($cred_file);
        $this->from_json($json);
    }
}

?>
