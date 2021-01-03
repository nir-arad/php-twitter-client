<?php

namespace NirArad\TwitterClient\oauth;

use NirArad\TwitterClient\Field;

class PostOauthRequestTokenParams extends Field\Container {

    public const ACCESS_TYPE_ENUM = array(
        "read",
        "write"
    );

    protected $_FIELDS = array(
        "oauth_callback" => [Field\Types::FIELD_STRING, null],
        "x_auth_access_type" => [Field\Types::FIELD_ENUM, PostOauthRequestTokenParams::ACCESS_TYPE_ENUM],
    );

    protected $_REQUIRED = array(
        "oauth_callback"
    );
}

?>
