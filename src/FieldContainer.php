<?php

namespace narad1972\TwitterClient;

use DateTimeInterface;
use UnexpectedValueException;

use narad1972\TwitterClient\FieldTypes;

class FieldContainer
{
    protected $_FIELDS = array();
    protected $_REQUIRED = array();
    protected $_values = array();

    protected function validate_required($var, $name, $required)
    {
        if ($required && is_null($var)) {
            throw new \Exception("Field " . $name . " must be provided\n");
        }
    }

    protected function validate_is_date($var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_a($var, 'DateTime')) {
            throw new \Exception("Field " . $name . " must be a DateTime object\n");
        }
    }

    protected function validate_is_int($var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_int($var)) {
            throw new \Exception("Field " . $name . " must be an integer\n");
        }
    }

    protected function validate_is_string($var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_a($var, 'String')) {
            throw new \Exception("Field " . $name . " must be a string\n");
        }
    }

    protected function validate_is_array($var, $name, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        if (!is_array($var)) {
            throw new \Exception("Field " . $name . " must be an array\n");
        }
    }

    protected function validate_is_enum($var, $name, $enum, $required = false)
    {
        if (is_null($var)) {
            $this->validate_required($var, $name, $required);
            return;
        }
        $diff = array_diff($var, $enum);
        if (!empty($diff)) {
            $msg = $name . " must not include the following elements: [" . implode(', ', $diff) . "].";
            throw new \Exception("Field " . $msg . "\n");
        }
    }

    public function from_array($query_params)
    {
        $_values = array();
        foreach ($this->_FIELDS as $name => &$validation) {
            $_values = Utils\array_get($query_params, $name, null);
        }
    }

    public function validate()
    {

        foreach ($this->_FIELDS as $name => &$validation) {
            $validator = $validation[1];
            $required = in_array($name, $this->_REQUIRED);

            switch ($validation[0]) {
                case FieldTypes::FIELD_INT:
                    $this->validate_is_int($this->_values[$name], $name, $required);
                    break;
                case FieldTypes::FIELD_STRING:
                    $this->validate_is_string($this->_values[$name], $name, $required);
                    break;
                case FieldTypes::FIELD_DATE:
                    $this->validate_is_date($this->_values[$name], $name, $required);
                    break;
                case FieldTypes::FIELD_ENUM:
                    $this->validate_is_enum($this->_values[$name], $name, $validator, $required);
                    break;
                case FieldTypes::FIELD_ARRAY:
                    $this->validate_is_array($this->_values[$name], $name, $required);
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
            if (!$this->_values[$name]) {
                continue;
            }
            $field = $name . "=";
            switch ($validation[0]) {
                case FieldTypes::FIELD_INT:
                case FieldTypes::FIELD_STRING:
                    $field .= $this->_values[$name];
                    break;
                case FieldTypes::FIELD_DATE:
                    $field .= $this->_values[$name]->format(DateTimeInterface::ISO8601);
                    break;
                case FieldTypes::FIELD_ENUM:
                case FieldTypes::FIELD_ARRAY:
                    $field .= implode(',', $this->_values[$name]);
                    break;
                default:
                    throw new UnexpectedValueException("Undefined field type\n");
            }

            $ret[] = $field;
        }

        $ret = implode('&', $ret);
        $ret = urlencode($ret);
        return $ret;
    }
}
