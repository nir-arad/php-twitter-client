<?php

declare(strict_types=1);

namespace narad1972\TwitterClient;

use PHPUnit\Framework\TestCase;

use narad1972\TwitterClient\ProjectCredentials;

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
    
    /**
     * Test that true does in fact equal true
     */
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }

    public function testSuccess() {
        $this->init();

        $user_info = $this->client->GetUsersByUsername("narad1972");
        
        $this->assertArrayHasKey("id", $user_info);
        $this->assertArrayHasKey("name", $user_info);
        $this->assertArrayHasKey("username", $user_info);
    }
}
