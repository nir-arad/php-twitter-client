<?php

namespace Nir-Arad\TwitterClient\v1\Tweets;

use Nir-Arad\TwitterClient\Field;

class PostStatusesUpdateParams extends Field\Container {
    protected $_FIELDS = array(
        "status" => [Field\Types::FIELD_STRING, null],
        "in_reply_to_status_id" => [Field\Types::FIELD_INT, null],
        "auto_populate_reply_metadata" => [Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM],
        "exclude_reply_user_ids" => [Field\Types::FIELD_INT, null],
        "attachment_url" => [Field\Types::FIELD_STRING, null],
        "media_ids" => [Field\Types::FIELD_INT_ARRAY, null],
        "possibly_sensitive" => [Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM],
        "lat" => [Field\Types::FIELD_STRING, null],
        "long" => [Field\Types::FIELD_STRING, null],
        "place_id" => [Field\Types::FIELD_STRING, null],
        "display_coordinates" => [Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM],
        "trim_user" => [Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM],
        "enable_dmcommands" => [Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM],
        "fail_dmcommands" => [Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM],
        "card_uri" => [Field\Types::FIELD_STRING, null],
    );

    protected $_REQUIRED = array(
        "status"
    );
}

?>
