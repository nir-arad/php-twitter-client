<?php

namespace narad1972\TwitterClient\v1\Tweets;

use InvalidArgumentException;

use narad1972\TwitterClient\Field;
use narad1972\TwitterClient\v1;

class GetStatusesLookupQueryParams extends Field\Container {
    protected $_FIELDS = array(
        "id" => array(Field\Types::FIELD_INT_ARRAY, null),
        "include_entities" => array(Field\Types::FIELD_ENUM, Field\Constants\BOOL_ENUM)
    );

}

?>
