<?php

namespace AradNir\TwitterClient\v2\Users;

use InvalidArgumentException;

use AradNir\TwitterClient\Field;
use AradNir\TwitterClient\v2;

class GetUserByIdQueryParams extends Field\Container {
    protected $_FIELDS = array(
        "expansions" => array(Field\Types::FIELD_ENUM_ARRAY, self::_EXPANSIONS_ENUM),
        "tweet.fields" => array(Field\Types::FIELD_ENUM_ARRAY, v2\Tweets\Constants::TWEET_FIELDS_ENUM),
        "user.fields" => array(Field\Types::FIELD_ENUM_ARRAY, v2\Users\Constants::USER_FIELDS_ENUM)
    );

    private const _EXPANSIONS_ENUM = array(
        "pinned_tweet_id"
    );
}

?>
