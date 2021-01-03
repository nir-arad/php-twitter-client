<?php

namespace Nir-Arad\TwitterClient\v1\Tweets;

use Nir-Arad\TwitterClient\Field;

class PostStatusesRetweetIdParams extends Field\Container {
    protected $_FIELDS = array(
        "id" => array(Field\Types::FIELD_INT_ARRAY, null),
        "trim_user" => array(Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM),
    );

    protected $_REQUIRED = array(
        "id"
    );
}

?>
