<?php

namespace narad1972\TwitterClient\v2\Users;

use InvalidArgumentException;

use narad1972\TwitterClient\FieldTypes;
use narad1972\TwitterClient\FieldContainer;
use narad1972\TwitterClient\v2;

class GetUsersQueryParams  extends FieldContainer {
    protected $_FIELDS = array(
        "expansions" => array(FieldTypes::FIELD_ENUM, self::_EXPANSIONS_ENUM),
        "ids" => array(FieldTypes::FIELD_ARRAY, null),
        "tweet.fields" => array(FieldTypes::FIELD_ENUM, v2\Tweets\Constants::TWEET_FIELDS_ENUM),
        "user.fields" => array(FieldTypes::FIELD_ENUM, v2\Users\Constants::USER_FIELDS_ENUM)
    );

    protected $_REQUIRED = array("ids");

    private const _EXPANSIONS_ENUM = array(
        "pinned_tweet_id"
    );

    private function _validate_ids() {
        $ids = &$this->_values["ids"];
        if (empty($ids)) {
            throw new InvalidArgumentException("'ids' field is empty\n");
        }
        if (sizeof($ids) > 100) {
            throw new InvalidArgumentException("'ids' field contains too many entries\n");
        }
        foreach ($ids as $id) {
            if (!is_int($id)) {
                throw new InvalidArgumentException("'ids' contains non-integer values\n");
            }
        }
    }

    public function validate()
    {
        $this->FieldContainer::validate();
        $this->_validate_ids();
    }

}

?>
