<?php

declare(strict_types=1);

namespace narad1972\TwitterClient;

use PHPUnit\Framework\TestCase;

use narad1972\TwitterClient\ProjectCredentials;

class TestGetUsers extends TestCase {

    /**
     * Test that true does in fact equal true
     */
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }

    public function testSuccess() {

        $CONFIG_DIR = 'config';
        // $CONFIG_FILE = $CONFIG_DIR . '/config.ini';
        $TWEETS_FILE = $CONFIG_DIR . '/tweets.json';
        $PROJECTS_FILE = $CONFIG_DIR . '/project.json';
        $USERS_FILE = $CONFIG_DIR . '/users.json';

        $projects_json = file_get_contents($PROJECTS_FILE);
        $project = json_decode($projects_json, true);

        $project_cred = new ProjectCredentials();
        $project_cred->from_array($project);
        
        $client = new TwitterClient();
        $client->project_credentials = $project_cred;

        $user_info = $client->GetUsersByUsername("narad1972");
        
        $this->assertArrayHasKey("id", $user_info);
        $this->assertArrayHasKey("name", $user_info);
        $this->assertArrayHasKey("username", $user_info);
    }
}
