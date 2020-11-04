<?php

declare(strict_types=1);

namespace narad1972\TwitterClient;

use PHPUnit\Framework\TestCase;

use narad1972\TwitterClient\ProjectCredentials;
use narad1972\TwitterClient\v2\Users\GetUsersQueryParams;

class TestGetUsers extends TestCase {

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
            "ids" => array(75625155)
        );
        $params->from_array($params_array);

        $user_info_array = $this->client->GetUsers($params);

        foreach ($user_info_array as $user_info) {
            $this->assertArrayHasKey("id", $user_info);
            $this->assertArrayHasKey("name", $user_info);
            $this->assertArrayHasKey("username", $user_info);
        }
    }
}
