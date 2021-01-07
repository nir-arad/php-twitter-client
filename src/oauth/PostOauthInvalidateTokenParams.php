<?php

namespace NirArad\TwitterClient\oauth;

use NirArad\TwitterClient\Field;

class PostOauthInvalidateTokenParams extends Field\Container {

    protected $_FIELDS = array(
        "oauth_token" => [Field\Types::FIELD_STRING, null],
    );

    protected $_REQUIRED = array(
        "oauth_token",
    );
}

?>
