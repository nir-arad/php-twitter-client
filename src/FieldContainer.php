<?php

namespace narad1972\TwitterClient;

use DateTimeInterface;
use UnexpectedValueException;

use narad1972\TwitterClient\FieldTypes;

use Exception;

class FieldContainer
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

    protected function validate_is_array(&$var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_array($var)) {
            throw new Exception("Field '" . $name . "' must be an array\n");
        }
    }

    protected function validate_is_enum(&$var, $name, $enum, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        $diff = array_diff($var, $enum);
        if (!empty($diff)) {
            $msg = "Field '" . $name . "' must not include the following elements: [" . implode(', ', $diff) . "]\n";
            throw new Exception($msg);
        }
    }

    public function from_array(&$query_params)
    {
        $this->_values = array();
        foreach ($this->_FIELDS as $name => &$validation) {
            $this->_values[$name] = array_get($query_params, $name, null);
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
                case FieldTypes::FIELD_INT:
                    $this->validate_is_int($val, $name, $required);
                    break;

                case FieldTypes::FIELD_STRING:
                    $this->validate_is_string($val, $name, $required);
                    break;

                case FieldTypes::FIELD_DATE:
                    $this->validate_is_date($val, $name, $required);
                    break;

                case FieldTypes::FIELD_ENUM:
                    $this->validate_is_enum($val, $name, $validator, $required);
                    break;

                case FieldTypes::FIELD_ARRAY:
                    $this->validate_is_array($val, $name, $required);
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
                case FieldTypes::FIELD_INT:
                case FieldTypes::FIELD_STRING:
                    $field .= urlencode($val);
                    break;

                case FieldTypes::FIELD_DATE:
                    $field .= urlencode($val->format(DateTimeInterface::ISO8601));
                    break;

                case FieldTypes::FIELD_ENUM:
                case FieldTypes::FIELD_ARRAY:
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
