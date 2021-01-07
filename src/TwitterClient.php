<?php

namespace NirArad\TwitterClient;

use RuntimeException;
use InvalidArgumentException;

use NirArad\TwitterClient\v1;
use NirArad\TwitterClient\v2;

require_once 'Utils.php';

class HttpMethod
{
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

class TwitterClient
{
    public $project_credentials;
    public $user_credentials;

    private $_curl_obj;

    public function __construct()
    {
        $this->_curl_obj = curl_init();
    }

    public function __destruct()
    {
        curl_close($this->_curl_obj);
    }

    private function validate_project(): void
    {
        if (!is_a($this->project_credentials, ProjectCredentials::class)) {
            throw new InvalidArgumentException("missing project credentials\n");
        }
    }

    private function validate_user(): void
    {
        if (!is_a($this->user_credentials, UserCredentials::class)) {
            throw new InvalidArgumentException("missing user credentials\n");
        }
    }

    private function curl_setopt_oauth_v2_bearer_token($url): void
    {
        $this->validate_project();
        $headers = array();
        $headers[] = 'Content-type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->project_credentials->bearer_token;

        curl_setopt($this->_curl_obj, CURLOPT_URL, $url);
        curl_setopt($this->_curl_obj, CURLOPT_HTTPHEADER, $headers);
    }

    private function curl_setopt_oauth_v1($method, $url): void
    {
        $this->validate_project();
        $this->validate_user();

        $oauth = new \OAuth($this->project_credentials->api_key, $this->project_credentials->api_secret, OAUTH_SIG_METHOD_HMACSHA1);
        $oauth->setToken($this->user_credentials->oauth_token, $this->user_credentials->oauth_token_secret);

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
        $auth_header .= 'oauth_token="' . urlencode($this->user_credentials->oauth_token) . '", ';
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

    /**
     * https://developer.twitter.com/en/docs/authentication/oauth-1-0a/creating-a-signature
     */
    private function oauth_generate_signature($method, $url, $key, $params): string
    {
        $enc_params = array();

        foreach ($params as $k => $v) {
            $enc_k = urlencode($k);
            $enc_v = urlencode($v);
            $enc_params[] = $enc_k . "=" . $enc_v;
        }

        ksort($enc_params);
        $param_string = implode("&", $enc_params);

        $base_string = strtoupper($method) . "&";
        $base_string .= urlencode($url) . "&";
        $base_string .= urlencode($param_string);

        $sig = base64_encode(hash_hmac("sha1", $base_string, $key, true));
        return $sig;
    }

    private function curl_setopt_oauth_user_access($method, $url, $params): void
    {
        $this->validate_project();

        $oauth = new \OAuth(
            $this->project_credentials->api_key,
            $this->project_credentials->api_secret,
            OAUTH_SIG_METHOD_HMACSHA1
        );

        $nonce = md5(microtime() . mt_rand());
        $oauth->setNonce($nonce);

        $timestamp = time();
        $oauth->setTimestamp($timestamp);

        $method_string = HttpMethod::to_string($method);

        $sig_key = $this->project_credentials->api_secret . "&";

        $sig_params = array_merge(
            $params,
            [
                'oauth_consumer_key' => $this->project_credentials->api_key,
                'oauth_nonce' => $nonce,
                'oauth_signature_method' => "HMAC-SHA1",
                'oauth_timestamp' => $timestamp,
                'oauth_version' => "1.0"
            ]
        );
        $sig = $this->oauth_generate_signature($method_string, $url, $sig_key, $sig_params);

        $parts = [
            'oauth_signature="' . urlencode($sig) . '"'
        ];
        foreach ($sig_params as $k => $v) {
            $parts[] = $k . '="' . urlencode($v) . '"';
        }

        $auth_header = "Authorization: OAuth ";
        $auth_header .= implode(", ", $parts);

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

    private function _validate_curl_exec($response): void
    {
        if ($response === false) {
            throw new RuntimeException("API call returned an error - " . curl_error($this->_curl_obj) . "\n");
        }

        $code = curl_getinfo($this->_curl_obj, CURLINFO_RESPONSE_CODE);
        if ($code != "200") {
            print("Response contents:\n");
            print_r($response);
            throw new RuntimeException("Bad response code: $code\n");
        }
    }

    /**
     * 3-legged OAuth flow: step 1
     * https://developer.twitter.com/en/docs/authentication/oauth-1-0a/obtaining-user-access-tokens
     */
    public function PostOauthRequestToken(oauth\PostOauthRequestTokenParams $params, $force = false): array
    {
        curl_reset($this->_curl_obj);

        if (!$force) {
            $params->validate();
        }

        $url = 'https://api.twitter.com/oauth/request_token';
        $this->curl_setopt_oauth_user_access(HttpMethod::POST, $url, $params->get());

        $response = curl_exec($this->_curl_obj);

        $this->_validate_curl_exec($response);
        $resp_array = explode("&", $response);
        $array = [];
        foreach ($resp_array as $part) {
            $parts = explode("=", $part);
            $k = $parts[0];
            $v = $parts[1];
            $array[$k] = $v;
        }

        return $array;
    }

    /**
     * 3-legged OAuth flow: step 3
     * https://developer.twitter.com/en/docs/authentication/oauth-1-0a/obtaining-user-access-tokens
     */
    public function PostOauthAccessToken(oauth\PostOauthAccessTokenParams $query_params, $force = false): array
    {
        curl_reset($this->_curl_obj);
        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/oauth/access_token';

        $this->curl_setopt_oauth_user_access(HttpMethod::POST, $url, $query_params->get());

        $response = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($response);
        $resp_array = explode("&", $response);
        $array = [];
        foreach ($resp_array as $part) {
            $parts = explode("=", $part);
            $k = $parts[0];
            $v = $parts[1];
            $array[$k] = $v;
        }

        return $array;
    }

    /**
     * Invalidate access token
     * https://developer.twitter.com/en/docs/authentication/api-reference/invalidate_access_token
     */
    public function PostOauthInvalidateToken(oauth\PostOauthInvalidateTokenParams $query_params, $force = false): array
    {
        curl_reset($this->_curl_obj);
        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/1.1/oauth/invalidate_token.json';

        $this->curl_setopt_oauth_user_access(HttpMethod::POST, $url, $query_params->get());

        $response = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($response);
        $resp_array = explode("&", $response);
        $array = [];
        foreach ($resp_array as $part) {
            $parts = explode("=", $part);
            $k = $parts[0];
            $v = $parts[1];
            $array[$k] = $v;
        }

        return $array;
    }

    /**
     * Retrieve multiple users with IDs
     * 
     * @param GetUsersQueryParams $query_params : query parameters
     * @param bool $force : force using $query_params without validation
     * 
     * @return array : an array of user entities
     */
    public function GetUsers(v2\Users\GetUsersQueryParams $query_params, $force = false): array
    {
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
        if (!$force) {
            $this->_validate_curl_exec($json);
        }
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
    public function GetUserByID(int $id, v2\Users\GetUserByIdQueryParams $query_params, $force = false): array
    {
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
    public function GetUsersBy()
    {
    }

    /**
     * Retrieve a single user with a usernames
     */
    public function GetUsersByUsername(string $user_name): array
    {
        curl_reset($this->_curl_obj);

        $url = 'https://api.twitter.com/2/users/by/username/' . $user_name;
        $this->curl_setopt_oauth_v2_bearer_token($url);
        curl_setopt($this->_curl_obj, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($this->_curl_obj);
        $this->_validate_curl_exec($json);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * https://developer.twitter.com/en/docs/twitter-api/tweets/search/api-reference/get-tweets-search-recent
     */
    public function GetTweetsSearchRecent(v2\Tweets\GetTweetsSearchRecentParams &$query_params, $force = false)
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
        $force = false
    ): array {

        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/1.1/statuses/lookup.json?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth_v1(HttpMethod::GET, $url);

        $json = curl_exec($this->_curl_obj);
        if (!$force) {
            $this->_validate_curl_exec($json);
        }
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * https://developer.twitter.com/en/docs/twitter-api/v1/tweets/post-and-engage/api-reference/post-statuses-update
     */
    public function PostStatusesUpdate(
        v1\Tweets\PostStatusesUpdateParams &$query_params,
        $force = false
    ): array {

        curl_reset($this->_curl_obj);

        if (!$force) {
            $query_params->validate();
        }

        $url = 'https://api.twitter.com/1.1/statuses/update.json?';
        $url .= $query_params->to_string();
        $this->curl_setopt_oauth_v1(HttpMethod::POST, $url);

        $json = curl_exec($this->_curl_obj);
        if (!$force) {
            $this->_validate_curl_exec($json);
        }
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * https://developer.twitter.com/en/docs/twitter-api/v1/tweets/post-and-engage/api-reference/post-statuses-retweet-id
     */
    public function PostStatusesRetweetId(
        v1\Tweets\PostStatusesRetweetIdParams &$query_params,
        $force = false
    ): array {

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
