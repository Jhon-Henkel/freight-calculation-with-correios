<?php

namespace src\Tools;

class StringTools
{
    public static function onlyNumbers(string $string): string
    {
        return (string)preg_replace("/[^0-9]/", "", $string);
    }
}