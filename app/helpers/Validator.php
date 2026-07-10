<?php

class Validator
{
    public static function required($value)
    {
        return trim($value) !== "";
    }

    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function minLength($text, $length)
    {
        return strlen($text) >= $length;
    }
}