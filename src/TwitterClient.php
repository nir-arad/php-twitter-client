<?php

namespace narad1972\TwitterClient;

use RuntimeException;
use InvalidArgumentException;

// use narad1972\TwitterClient\ProjectCredentials;
// use narad1972\TwitterClient\UserCredentials;
use narad1972\TwitterClient\v1;
use narad1972\TwitterClient\v2;

require_once 'Utils.php';

class HttpMethod {
    const GET = 1;
    const POST = 2;
    const PUT = 3;
    const DELETE = 4;

    private static $method_map = [
        HttpMethod::GET => 'GET',
        HttpMethod::POST => 'POST',
        HttpMethod::PUT => 'PUT',
        HttpMethod::DELETE => 'DELETE',
    ];

    public static function to_string($method_id)
    {
        if (isset(static::$method_map[$method_id])) {
            return static::$method_map[$method_id];
        }
    }
}

class TwitterClient {
    public $project_credentials;
    public $user_credentials;

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

    private function curl_setopt_oauth_v2_bearer_token($url) : void {
        $this->validate_project();
        $headers = array();
        $headers[] = 'Content-type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->project_credentials->bearer_token;
    
        curl_setopt($this->_curl_obj, CURLOPT_URL, $url);
        curl_setopt($this->_curl_obj, CURLOPT_HTTPHEADER, $headers);
    }

    private function curl_setopt_oauth_v1($method, $url) : void {
        $this->validate_project();
        $this->validate_user();

        $oauth = new \OAuth($this->project_credentials->api_key, $this->project_credentials->api_secret);
        $oauth->setToken($this->user_credentials->access_token, $this->user_credentials->access_token_secret);
    
        $nonce = mt_rand();
        $oauth->setNonce($nonce);
    
        $timestamp = time();
        $oauth->setTimestamp($timestamp);

        $method_string = HttpMethod::to_string($method);
        $sig = $oauth->generateSignature($method_string, $url);
    
        $auth_header = "Authorization: OAuth ";
        $auth_header .= 'oauth_consumer_key="' . urlencode($this->project_credentials->api_key) . '", ';
        $auth_header .= 'oauth_nonce="' . $nonce . '", ';
        $auth_header .= 'oauth_signature="' . urlencode($sig) . '", ';
        $auth_header .= 'oauth_signature_method="HMAC-SHA1", ';
        $auth_header .= 'oauth_timestamp="' . $timestamp . '", ';
        $auth_header .= 'oauth_token="' . urlencode($this->user_credentials->access_token) . '", ';
        $auth_header .= 'oauth_version="1.0"';
    
        $headers = array();
        $headers[] = 'Content-type: application/json';
        $headers[] = $auth_header;
    
        curl_setopt($this->_curl_obj, CURLOPT_URL, $url);
        curl_setopt($this->_curl_obj, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);

        if ($method == HttpMethod::POST) {
            curl_setopt($this->_curl_obj, CURLOPT_POST, true);
        }
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
    public function GetUsers(v2\Users\GetUsersQueryParams $query_params, $force=false) : array {
        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }
        $query_string = $query_params->to_string();

        $url = 'https://api.twitter.com/2/users?';
        $url .= $query_string;
        $this->curl_setopt_oauth_v2_bearer_token($url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * Retrieve a single user with an ID
     * 
     * @param int $id : the user-id of the requested user
     * @param GetUserByIdQueryParams $query_params : query parameters
     * @param bool $force : force using $query_params without validation
     * 
     * @return array : an associative array with user information
     */
    public function GetUserByID(int $id, v2\Users\GetUserByIdQueryParams $query_params, $force=false) : array {
        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }
        $query_string = $query_params->to_string();

        $url = 'https://api.twitter.com/2/users/' . $id;
        $url .= "?" . $query_string;
        $this->curl_setopt_oauth_v2_bearer_token($url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
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
        $this->curl_setopt_oauth_v2_bearer_token($url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }
    
    public function GetTweetsSearchRecent(string $user_name, v2\Tweets\Search\RecentQueryParams &$query_params, $force=false)
    {
        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/2/tweets/search/recent?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth_v2_bearer_token($url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * GetStatusesLookup
     */
    public function GetStatusesLookup(
        v1\Tweets\GetStatusesLookupQueryParams &$query_params,
        $force=false) : array {

        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/1.1/statuses/lookup.json?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth_v1(HttpMethod::GET, $url);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * https://developer.twitter.com/en/docs/twitter-api/v1/tweets/post-and-engage/api-reference/post-statuses-update
     */
    public function PostStatusesUpdate(
        v1\Tweets\PostStatusesUpdateParams &$query_params,
        $force=false
    ) : array {

        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/1.1/statuses/update.json?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth_v1(HttpMethod::POST, $url);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * https://developer.twitter.com/en/docs/twitter-api/v1/tweets/post-and-engage/api-reference/post-statuses-retweet-id
     */
    public function PostStatusesRetweetId(
        v1\Tweets\PostStatusesRetweetIdParams &$query_params,
        $force=false
    ) : array {

        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/1.1/statuses/update.json?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth_v1(HttpMethod::POST, $url);
    
        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }

}

?>