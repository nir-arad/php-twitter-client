<?php

namespace NirArad\TwitterClient\v1\Tweets;

use InvalidArgumentException;

use NirArad\TwitterClient\Field;
use NirArad\TwitterClient\v1;

class GetStatusesLookupQueryParams extends Field\Container {
    protected $_FIELDS = array(
        "id" => array(Field\Types::FIELD_INT_ARRAY, null),
        "include_entities" => array(Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM),
        "trim_user" => array(Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM),
        "map" => array(Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM),
        "include_ext_alt_text" => array(Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM),
        "include_card_uri" => array(Field\Types::FIELD_ENUM, Field\Constants::BOOL_ENUM)
    );

    protected $_REQUIRED = array(
        "id"
    );
}

?>
