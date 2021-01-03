<?php

declare(strict_types=1);

namespace NirArad\TwitterClientTest;

use PHPUnit\Framework\TestCase;

use NirArad\TwitterClient;
use NirArad\TwitterClient\ProjectCredentials;
use NirArad\TwitterClient\v1;

class GetStatusesLookupTest extends TestCase {

    private $client;

    private function get_project_credentials() {
        $fields = array(
            "bearer_token",
            "api_key",
            "api_secret"
        );
        $cred_array = array();

        $cred_filename = 'config/project.json';

        if (file_exists($cred_filename)) {
            // Local testing - take credentials from configuration file
            $cred_json = file_get_contents($cred_filename);
            $cred_array = json_decode($cred_json, true);
        } else {
            // Gitlab CI - take credentials from environment
            foreach ($fields as $field) {
                $env_var = "project_" . $field;
                isset($_ENV[$env_var]) && $cred_array[$field] = $_ENV[$env_var];
            }
        }

        $cred_obj = new ProjectCredentials();
        $cred_obj->from_array($cred_array);
        return $cred_obj;
    }

    private function get_user_credentials() {
        $fields = array(
            "access_token",
            "access_token_secret"
        );
        $cred_array = array();

        $cred_filename = 'config/users.json';

        if (file_exists($cred_filename)) {
            // Local testing - take credentials from configuration file
            $cred_json = file_get_contents($cred_filename);
            $cred_array = json_decode($cred_json, true)[0];
        } else {
            // Gitlab CI - take credentials from environment
            foreach ($fields as $field) {
                $env_var = "user_" . $field;
                isset($_ENV[$env_var]) && $cred_array[$field] = $_ENV[$env_var];
            }
        }

        $cred_obj = new TwitterClient\UserCredentials();
        $cred_obj->from_array($cred_array);
        return $cred_obj;
    }

    private function init() {
        $this->client = new TwitterClient\TwitterClient();
        $this->client->project_credentials = $this->get_project_credentials();
        $this->client->user_credentials = $this->get_user_credentials();
    }
    
    public function testSuccess() {
        $this->init();

        $params = new v1\Tweets\GetStatusesLookupQueryParams();
        $params_array = array(
            "id" => array(1326023218772144134)
        );
        $params->from_array($params_array);

        $response = $this->client->GetStatusesLookup($params);

        // echo __METHOD__ . "\n";
        // var_dump($response);

        foreach ($response as $status) {
            $user_info = $status["user"];
            $this->assertArrayHasKey("id", $user_info);
            $this->assertArrayHasKey("name", $user_info);
            $this->assertArrayHasKey("screen_name", $user_info);
        }
    }

    public function testSuccessWithOptions() {
        $this->init();

        $params = new v1\Tweets\GetStatusesLookupQueryParams();
        $params_array = array(
            "id" => array(1326023218772144134, 1326011125083729920),
            "trim_user" => "false"
            );
        $params->from_array($params_array);

        $response = $this->client->GetStatusesLookup($params);

        // echo __METHOD__ . "\n";
        // var_dump($response);

        foreach ($response as $status) {
            $user_info = $status["user"];
            $this->assertArrayHasKey("id", $user_info);
            $this->assertArrayHasKey("name", $user_info);
            $this->assertArrayHasKey("screen_name", $user_info);
            $this->assertArrayHasKey("profile_image_url", $user_info);
        }
    }

    public function testError() {
        $this->init();

        $params = new v1\Tweets\GetStatusesLookupQueryParams();
        $params_array = array();
        $params->from_array($params_array);

        $response = $this->client->GetStatusesLookup($params, $forced = true);

        // echo __METHOD__ . "\n";
        // var_dump($response);

        $this->assertArrayHasKey("errors", $response);

        foreach ($response["errors"] as $error) {
            $this->assertArrayHasKey("code", $error);
            $this->assertArrayHasKey("message", $error);
        }
    }
}
