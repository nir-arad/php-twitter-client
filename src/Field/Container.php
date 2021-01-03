<?php

namespace Nir-Arad\TwitterClient\Field;

use DateTimeInterface;
use UnexpectedValueException;

use Nir-Arad\TwitterClient\Utils;
use Nir-Arad\TwitterClient\Field;

use Exception;

class Container
{
    protected $_FIELDS = array();
    protected $_REQUIRED = array();
    protected $_values = array();

    protected function validate_is_date(&$var, $name)
    {
        if (!is_a($var, 'DateTime')) {
            throw new Exception("Field '" . $name . "' must be a DateTime object\n");
        }
    }

    protected function validate_is_int(&$var, $name)
    {
        if (!is_int($var)) {
            throw new Exception("Field '" . $name . "' must be an integer\n");
        }
    }

    protected function validate_is_string(&$var, $name)
    {
        if (!is_string($var)) {
            throw new Exception("Field '" . $name . "' must be a string\n");
        }
    }

    protected function validate_is_enum(&$var, $name, &$enum)
    {
        if (!in_array($var, $enum)) {
            throw new Exception("Enum field '" . $name . "' contains non enumerated value\n");
        }
    }

    protected function validate_is_int_array(&$var, $name)
    {
        if (!is_array($var)) {
            throw new Exception("Integer array field '" . $name . "' must be an array\n");
        }
        foreach ($var as $val) {
            if (!is_int($val)) {
                throw new Exception("Integer array field '" . $name . "': value <" . $val . "> is not an integer\n");
            }
        }
    }

    protected function validate_is_enum_array(&$var, $name, $enum)
    {
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
        foreach ($query_params as $k => $v) {
            if (array_key_exists($k, $this->_FIELDS)) {
                $this->_values[$k] = $v;
            }
        }
    }

    public function validate()
    {

        foreach ($this->_FIELDS as $name => &$validation) {
            $type = $validation[0];
            $validator = $validation[1];
            $required = in_array($name, $this->_REQUIRED);

            if (array_key_exists($name, $this->_values)) {
                $val = &$this->_values[$name];
            } else if ($required) {
                throw new Exception("Field '" . $name . "' must be provided\n");
            } else {
                continue;
            }
    
            switch ($type) {
                case Field\Types::FIELD_INT:
                    $this->validate_is_int($val, $name);
                    break;

                case Field\Types::FIELD_STRING:
                    $this->validate_is_string($val, $name);
                    break;

                case Field\Types::FIELD_DATE:
                    $this->validate_is_date($val, $name);
                    break;

                case Field\Types::FIELD_ENUM:
                    $this->validate_is_enum($val, $name, $validator);
                    break;

                case Field\Types::FIELD_INT_ARRAY:
                    $this->validate_is_int_array($val, $name);
                    break;

                case Field\Types::FIELD_ENUM_ARRAY:
                    $this->validate_is_enum_array($val, $name, $validator);
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
            if (!array_key_exists($name, $this->_values)) continue;

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

    public function get() : array {
        return $this->_values;
    }
}
