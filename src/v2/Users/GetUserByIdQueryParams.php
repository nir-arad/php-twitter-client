<?php

namespace narad1972\TwitterClient\v2\Users;

use InvalidArgumentException;

use narad1972\TwitterClient\FieldTypes;
use narad1972\TwitterClient\FieldContainer;
use narad1972\TwitterClient\v2;

class GetUserByIdQueryParams extends FieldContainer {
    protected $_FIELDS = array(
        "expansions" => array(FieldTypes::FIELD_ENUM, self::_EXPANSIONS_ENUM),
        "tweet.fields" => array(FieldTypes::FIELD_ENUM, v2\Tweets\Constants::TWEET_FIELDS_ENUM),
        "user.fields" => array(FieldTypes::FIELD_ENUM, v2\Users\Constants::USER_FIELDS_ENUM)
    );

    private const _EXPANSIONS_ENUM = array(
        "pinned_tweet_id"
    );
}

?>
