<?php

namespace Nir-Arad\TwitterClient;

use Nir-Arad\TwitterClient\Utils;

class UserCredentials {
    public $screen_name;
    public $user_id;
    public $oauth_token;
    public $oauth_token_secret;

    public function __construct($screen_name = null, $user_id = null, $user_oauth_token = null, $user_oauth_token_secret = null)
    {
        $this->screen_name = $screen_name;
        $this->user_id = $user_id;
        $this->oauth_token = $user_oauth_token;
        $this->oauth_token_secret = $user_oauth_token_secret;
    }

    public function from_array($credentials) {
        $this->screen_name = Utils::array_get($credentials, 'screen_name', '');
        $this->user_id = Utils::array_get($credentials, 'user_id', '');
        $this->oauth_token = Utils::array_get($credentials, 'oauth_token' , null);
        $this->oauth_token_secret = Utils::array_get($credentials, 'oauth_token_secret', null);
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
