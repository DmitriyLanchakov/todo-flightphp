<?php

namespace Util\Util;

class Util
{
    public static function validPasswords($password, $password2)
    {
        return ($password == $password2);

    }

    public static function validEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function hash($email, $password)
    {
        return md5($email.$password);
    }
}