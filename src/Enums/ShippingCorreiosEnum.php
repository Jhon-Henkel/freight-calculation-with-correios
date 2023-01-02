<?php

namespace src\Enums;

class ShippingCorreiosEnum
{
    const PAC = 41106;
    const SEDEX = 40010;
    const SEND_FORMAT_BOX = 1;

    public static function getDescriptionFromCode(int $code): string
    {
        return match ($code) {
            self::SEDEX => 'SEDEX',
            self::PAC => 'PAC',
            default => 'Não encontrado',
        };
    }
}