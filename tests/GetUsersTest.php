<?php

declare(strict_types=1);

namespace NirArad\TwitterClient;

use PHPUnit\Framework\TestCase;

use NirArad\TwitterClient\ProjectCredentials;
use NirArad\TwitterClient\v2\Users\GetUsersQueryParams;

class GetUsersTest extends TestCase {

    private $client;

    private function init() {
        $project_cred_fields = array(
            "bearer_token",
            "api_key",
            "api_secret"
        );
        $project_cred_array = array();

        $projects_filename = 'config/project.json';

        if (file_exists($projects_filename)) {
            // Local testing - take credentials from configuration file
            $projects_json = file_get_contents($projects_filename);
            $project_cred_array = json_decode($projects_json, true);
        } else {
            // Gitlab CI - take credentials from environment
            foreach ($project_cred_fields as $field) {
                $env_var = "project_" . $field;
                isset($_ENV[$env_var]) && $project_cred_array[$field] = $_ENV[$env_var];
            }
        }

        $project_cred = new ProjectCredentials();
        $project_cred->from_array($project_cred_array);
        
        $this->client = new TwitterClient();
        $this->client->project_credentials = $project_cred;
    }
    
    public function testSuccess() {
        $this->init();

        $params = new GetUsersQueryParams();
        $params_array = array(
            "ids" => array(783214)
        );
        $params->from_array($params_array);

        $response = $this->client->GetUsers($params);

        foreach ($response["data"] as $user_info) {
            $this->assertArrayHasKey("id", $user_info);
            $this->assertArrayHasKey("name", $user_info);
            $this->assertArrayHasKey("username", $user_info);
        }
    }

    public function testSuccessWithOptions() {
        $this->init();

        $params = new GetUsersQueryParams();
        $params_array = array(
            "ids" => array(2244994945, 75625155),
            "expansions" => array("pinned_tweet_id"),
            "tweet.fields" => array("created_at"),
            "user.fields" => array("profile_image_url", "verified")
            );
        $params->from_array($params_array);

        $response = $this->client->GetUsers($params);

        foreach ($response["data"] as $user_info) {
            $this->assertArrayHasKey("id", $user_info);
            $this->assertArrayHasKey("name", $user_info);
            $this->assertArrayHasKey("username", $user_info);
            $this->assertArrayHasKey("profile_image_url", $user_info);
        }
    }

    public function testError() {
        $this->init();

        $params = new GetUsersQueryParams();
        $params_array = array();
        $params->from_array($params_array);

        $response = $this->client->GetUsers($params, $forced = true);

        $this->assertArrayHasKey("title", $response);
        $this->assertArrayHasKey("detail", $response);
        $this->assertArrayHasKey("type", $response);
        $this->assertArrayHasKey("errors", $response);

        $this->assertEquals("Invalid Request", $response["title"]);
        foreach ($response["errors"] as $error) {
            $this->assertArrayHasKey("parameters", $error);
            $this->assertArrayHasKey("message", $error);
        }
    }
}
