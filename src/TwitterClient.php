<?php

namespace narad1972\TwitterClient;

use RuntimeException;
use InvalidArgumentException;

use narad1972\TwitterClient\ProjectCredentials;
use narad1972\TwitterClient\UserCredentials;
use narad1972\TwitterClient\v2\Tweets\Search\RecentQueryParams;
use narad1972\TwitterClient\v2\Users\GetUsersQueryParams;

require_once 'Utils.php';

class TwitterClient {
    public $project_credentials = null;
    public $user_credentials = null;

    private $_curl_obj;

    public function __construct() {
        $this->_curl_obj = curl_init();
    }

    public function __destruct() {
        curl_close($this->_curl_obj);
    }

    private function validate_project() : void {
        if (!is_a($this->project_credentials, ProjectCredentials::class)) {
            throw new InvalidArgumentException("missing project credentials\n");
        }
    }

    private function validate_user() : void {
        if (!is_a($this->user_credentials, UserCredentials::class)) {
            throw new InvalidArgumentException("missing user credentials\n");
        }
    }

    private function curl_setopt_oauth2_bearer_token() : void {
        $this->validate_project();
        $headers = array();
        $headers[] = 'Content-type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->project_credentials->bearer_token;
    
        curl_setopt($this->_curl_obj, CURLOPT_HTTPHEADER, $headers);
    }

    private function _validate_curl_exec($response) : void {
        if ($response === false) {
            throw new RuntimeException("API call returned an error - " . curl_error($this->_curl_obj) . "\n");
        }
    }

    /**
     * Retrieve multiple users with IDs
     * 
     * @param GetUsersQueryParams $query_params : query parameters
     * @param bool $force : force using $query_params without validation
     * 
     * @return array : an array of user entities
     */
    public function GetUsers(GetUsersQueryParams $query_params, $force=false) : array {
        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }
        $query_string = $query_params->to_string();

        $url = 'https://api.twitter.com/2/users?';
        $url .= $query_string;
        $this->curl_setopt_oauth2_bearer_token();
        curl_setopt($this->_curl_obj, CURLOPT_URL, $url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array["data"];
    }

    /**
     * Retrieve a single user with an ID
     */
    public function GetUsersByID() {

    }

    /**
     * Retrieve multiple users with usernames
     */
    public function GetUsersBy() {

    }

    /**
     * Retrieve a single user with a usernames
     */
    public function GetUsersByUsername(string $user_name) : array {
        curl_reset($this->_curl_obj);

        $url = 'https://api.twitter.com/2/users/by/username/' . $user_name;
        $this->curl_setopt_oauth2_bearer_token();
        curl_setopt($this->_curl_obj, CURLOPT_URL, $url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array["data"];
    }
    
    public function GetTweetsSearchRecent(string $user_name, RecentQueryParams &$query_params, $force=false)
    {
        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/2/tweets/search/recent?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth2_bearer_token();
        curl_setopt($this->_curl_obj, CURLOPT_URL, $url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }
        
}

?>