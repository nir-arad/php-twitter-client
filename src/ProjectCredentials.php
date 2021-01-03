<?php

namespace NirArad\TwitterClient;

class ProjectCredentials {
    public $bearer_token;
    public $api_key;
    public $api_secret;

    public function __construct($project_bearer_token = null, $project_api_key = null, $project_api_secret = null)
    {
        $this->bearer_token = $project_bearer_token;
        $this->api_key = $project_api_key;
        $this->api_secret = $project_api_secret;
    }

    public function from_array($credentials) {
        $this->bearer_token = $credentials['bearer_token'];
        $this->api_key = $credentials['api_key'];
        $this->api_secret = $credentials['api_secret'];
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
