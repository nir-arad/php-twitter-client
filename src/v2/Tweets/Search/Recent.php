<?php

namespace narad1972\TwitterClient\v2\Tweets\Search;

use narad1972\TwitterClient\Field;
use narad1972\TwitterClient\v2;

class RecentQueryParams extends Field\Container {

    protected $_FIELDS = array(
        "end_time" => array(Field\Types::FIELD_DATE, null),
        "expansions" => array(Field\Types::FIELD_ENUM_ARRAY, self::_EXPANSIONS_ENUM),
        "max_results" => array(Field\Types::FIELD_INT, null),
        "media.fields" => array(Field\Types::FIELD_ENUM_ARRAY, self::_MEDIA_FIELDS_ENUM),
        "next_token" => array(Field\Types::FIELD_STRING, null),
        "place.fields" => array(Field\Types::FIELD_ENUM_ARRAY, self::_PLACE_FIELDS_ENUM),
        "poll.fields" => array(Field\Types::FIELD_ENUM_ARRAY, self::_POLL_FIELDS_ENUM),
        "query" => array(Field\Types::FIELD_STRING, null),
        "since_id" => array(Field\Types::FIELD_STRING, null),
        "start_time" => array(Field\Types::FIELD_DATE, null),
        "tweet.fields" => array(Field\Types::FIELD_ENUM_ARRAY, v2\Tweets\Constants::TWEET_FIELDS_ENUM),
        "until_id" => array(Field\Types::FIELD_STRING, null),
        "user.fields" => array(Field\Types::FIELD_ENUM_ARRAY, v2\Users\Constants::USER_FIELDS_ENUM)
    );

    protected $_REQUIRED = array("query");

    private const _EXPANSIONS_ENUM = array(
        "attachments.poll_ids",
        "attachments.media_keys",
        "author_id",
        "entities.mentions.username",
        "geo.place_id",
        "in_reply_to_user_id",
        "referenced_tweets.id",
        "referenced_tweets.id.author_id"
    );

    private const _MAX_RESULTS_MIN = 10;

    private const _MEDIA_FIELDS_ENUM = array(
        "duration_ms",
        "height",
        "media_key",
        "preview_image_url",
        "type",
        "url",
        "width",
        "public_metrics",
        "non_public_metrics",
        "organic_metrics",
        "promoted_metrics"
    );

    private const _PLACE_FIELDS_ENUM = array(
        "contained_within",
        "country",
        "country_code",
        "full_name",
        "geo",
        "id",
        "name",
        "place_type"
    );

    private const _POLL_FIELDS_ENUM = array(
        "duration_minutes",
        "end_datetime",
        "id",
        "options",
        "voting_status"
    );

    public function validate()
    {
        $this->FieldContainer::validate();
        if (!is_null($this->_values["max_results"])) {
            if ($this->_values["max_results"] < $this->_MAX_RESULTS_MIN) {
                throw new \Exception("Search for recent tweets: max_results must be higher than " . self::_MAX_RESULTS_MIN . ".");
            }
        }
    }

}
