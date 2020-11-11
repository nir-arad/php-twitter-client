<?php

namespace narad1972\TwitterClient\Field;

use DateTimeInterface;
use UnexpectedValueException;

use narad1972\TwitterClient\Utils;
use narad1972\TwitterClient\Field;

use Exception;

class Container
{
    protected $_FIELDS = array();
    protected $_REQUIRED = array();
    protected $_values = array();

    protected function validate_required(&$var, $name, $required)
    {
        if ($required && is_null($var)) {
            throw new Exception("Field '" . $name . "' must be provided\n");
        }
    }

    protected function validate_is_date(&$var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_a($var, 'DateTime')) {
            throw new Exception("Field '" . $name . "' must be a DateTime object\n");
        }
    }

    protected function validate_is_int(&$var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_int($var)) {
            throw new Exception("Field '" . $name . "' must be an integer\n");
        }
    }

    protected function validate_is_string(&$var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_a($var, 'String')) {
            throw new Exception("Field '" . $name . "' must be a string\n");
        }
    }

    protected function validate_is_enum(&$var, $name, &$enum, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!in_array($var, $enum)) {
            throw new Exception("Enum field '" . $name . "' contains non enumerated value\n");
        }
    }

    protected function validate_is_int_array(&$var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_array($var)) {
            throw new Exception("Integer array field '" . $name . "' must be an array\n");
        }
        foreach ($var as $val) {
            if (!is_int($val)) {
                throw new Exception("Integer array field '" . $name . "': value <" . $val . "> is not an integer\n");
            }
        }
    }

    protected function validate_is_enum_array(&$var, $name, $enum, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_array($var)) {
            throw new Exception("Enum array field '" . $name . "' must be an array\n");
        }
        $diff = array_diff($var, $enum);
        if (!empty($diff)) {
            $msg = "Enum array field '" . $name . "' must not include the following elements: [" . implode(', ', $diff) . "]\n";
            throw new Exception($msg);
        }
    }

    public function from_array(&$query_params)
    {
        $this->_values = array();
        foreach ($this->_FIELDS as $name => &$validation) {
            $this->_values[$name] = Utils::array_get($query_params, $name, null);
        }
    }

    public function validate()
    {

        foreach ($this->_FIELDS as $name => &$validation) {
            $validator = $validation[1];
            $required = in_array($name, $this->_REQUIRED);

            $type = $validation[0];
            $val = &$this->_values[$name];

            switch ($type) {
                case Field\Types::FIELD_INT:
                    $this->validate_is_int($val, $name, $required);
                    break;

                case Field\Types::FIELD_STRING:
                    $this->validate_is_string($val, $name, $required);
                    break;

                case Field\Types::FIELD_DATE:
                    $this->validate_is_date($val, $name, $required);
                    break;

                case Field\Types::FIELD_ENUM:
                    $this->validate_is_enum($val, $name, $validator, $required);
                    break;

                case Field\Types::FIELD_INT_ARRAY:
                    $this->validate_is_int_array($val, $name, $required);
                    break;

                case Field\Types::FIELD_ENUM_ARRAY:
                    $this->validate_is_enum_array($val, $name, $validator, $required);
                    break;

                default:
                    throw new UnexpectedValueException("Undefined field type\n");
            }
        }
    }

    public function to_string()
    {
        $ret = [];
        foreach ($this->_FIELDS as $name => &$validation) {
            if (!$this->_values[$name]) continue;

            $field = $name . "=";
            $type = $validation[0];
            $val = &$this->_values[$name];

            switch ($type) {
                case Field\Types::FIELD_INT:
                case Field\Types::FIELD_STRING:
                case Field\Types::FIELD_ENUM:
                    $field .= urlencode($val);
                    break;

                case Field\Types::FIELD_DATE:
                    $field .= urlencode($val->format(DateTimeInterface::ISO8601));
                    break;

                case Field\Types::FIELD_INT_ARRAY:
                case Field\Types::FIELD_ENUM_ARRAY:
                    $field .= urlencode(implode(',', $val));
                    break;

                default:
                    throw new UnexpectedValueException("Undefined field type\n");
            }

            $ret[] = $field;
        }

        $ret = implode('&', $ret);
        return $ret;
    }
}
