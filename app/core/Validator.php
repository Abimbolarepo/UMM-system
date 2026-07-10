<?php

class Validator
{

    private $errors = [];

    public function required($field,$value)
    {
        if(trim($value) == ''){
            $this->errors[$field] = "$field is required.";
        }

        return $this;
    }

    public function email($field,$value)
    {
        if(!filter_var($value,FILTER_VALIDATE_EMAIL)){
            $this->errors[$field] = "Invalid email address.";
        }

        return $this;
    }

    public function min($field,$value,$length)
    {
        if(strlen($value) < $length){
            $this->errors[$field] =
                "$field must be at least $length characters.";
        }

        return $this;
    }

    public function max($field,$value,$length)
    {
        if(strlen($value) > $length){
            $this->errors[$field] =
                "$field must not exceed $length characters.";
        }

        return $this;
    }

    public function matches($field,$value,$compare)
    {
        if($value !== $compare){
            $this->errors[$field] =
                "$field does not match.";
        }

        return $this;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function passes()
    {
        return empty($this->errors);
    }

}