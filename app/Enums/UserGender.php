<?php

namespace App\Enums;

enum UserGender: string
{
    case Male = 'Laki-laki';
    case Female = 'Perempuan';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
