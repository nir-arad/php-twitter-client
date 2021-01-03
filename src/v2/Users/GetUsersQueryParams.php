<?php

namespace Nir-Arad\TwitterClient\v2\Users;

use InvalidArgumentException;

use Nir-Arad\TwitterClient\Field;
use Nir-Arad\TwitterClient\v2;

class GetUsersQueryParams extends Field\Container {
    protected $_FIELDS = array(
        "expansions" => array(Field\Types::FIELD_ENUM_ARRAY, self::_EXPANSIONS_ENUM),
        "ids" => array(Field\Types::FIELD_INT_ARRAY, null),
        "tweet.fields" => array(Field\Types::FIELD_ENUM_ARRAY, v2\Tweets\Constants::TWEET_FIELDS_ENUM),
        "user.fields" => array(Field\Types::FIELD_ENUM_ARRAY, v2\Users\Constants::USER_FIELDS_ENUM)
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
        parent::validate();
        $this->_validate_ids();
    }

}

?>
