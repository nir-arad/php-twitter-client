<?php

namespace Nir-Arad\TwitterClient\oauth;

use Nir-Arad\TwitterClient\Field;

class PostOauthAccessTokenParams extends Field\Container {

    protected $_FIELDS = array(
        "oauth_token" => [Field\Types::FIELD_STRING, null],
        "oauth_verifier" => [Field\Types::FIELD_STRING, null],
    );

    protected $_REQUIRED = array(
        "oauth_token",
        "oauth_verifier"
    );
}

?>
