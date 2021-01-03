<?php

declare(strict_types=1);

namespace TwitterClient;

use PHPUnit\Framework\TestCase;

use TwitterClient\ProjectCredentials;
use TwitterClient\v1;

class PostStatusesUpdateTest extends TestCase
{

    private $client;

    private function get_project_credentials()
    {
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

    private function get_user_credentials()
    {
        $fields = array(
            "access_token",
            "access_token_secret"
        );
        $cred_array = array();

        $cred_filename = 'config/user.json';

        if (file_exists($cred_filename)) {
            // Local testing - take credentials from configuration file
            $cred_json = file_get_contents($cred_filename);
            $cred_array = json_decode($cred_json, true);
        } else {
            // Gitlab CI - take credentials from environment
            foreach ($fields as $field) {
                $env_var = "user_" . $field;
                isset($_ENV[$env_var]) && $cred_array[$field] = $_ENV[$env_var];
            }
        }

        $cred_obj = new UserCredentials();
        $cred_obj->from_array($cred_array);
        return $cred_obj;
    }

    protected function setUp(): void
    {
        $this->client = new TwitterClient();
        $this->client->project_credentials = $this->get_project_credentials();
        $this->client->user_credentials = $this->get_user_credentials();
    }

    public function testSuccess()
    {
        $params = new v1\Tweets\PostStatusesUpdateParams();
        $msg = "testSuccess(): this is a test tweet ";
        $msg = $msg . strval(mt_rand());
        $params_array = array(
            "status" => $msg
        );
        $params->from_array($params_array);

        $response = $this->client->PostStatusesUpdate($params);

        // echo __METHOD__ . "\n";
        // var_dump($response);

        $user_info = $response["user"];
        $this->assertArrayHasKey("id", $user_info);
        $this->assertArrayHasKey("name", $user_info);
        $this->assertArrayHasKey("screen_name", $user_info);
    }

    public function testSuccessWithOptions()
    {
        $params = new v1\Tweets\PostStatusesUpdateParams();
        $msg = "testSuccessWithOptions(): this is a test tweet ";
        $msg = $msg . strval(mt_rand());
        $params_array = array(
            "status" => $msg,
            "trim_user" => "false"
        );
        $params->from_array($params_array);

        $response = $this->client->PostStatusesUpdate($params);

        // echo __METHOD__ . "\n";
        // var_dump($response);

        $user_info = $response["user"];
        $this->assertArrayHasKey("id", $user_info);
        $this->assertArrayHasKey("name", $user_info);
        $this->assertArrayHasKey("screen_name", $user_info);
        $this->assertArrayHasKey("profile_image_url", $user_info);
    }

    public function testError()
    {
        $params = new v1\Tweets\PostStatusesUpdateParams();
        $params_array = array();
        $params->from_array($params_array);

        $response = $this->client->PostStatusesUpdate($params, $forced = true);

        // echo __METHOD__ . "\n";
        // var_dump($response);

        $this->assertArrayHasKey("errors", $response);

        foreach ($response["errors"] as $error) {
            $this->assertArrayHasKey("code", $error);
            $this->assertArrayHasKey("message", $error);
        }
    }
}
