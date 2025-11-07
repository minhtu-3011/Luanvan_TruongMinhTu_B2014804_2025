<?php

namespace App\Enums;


enum SlideEnum: string
{

    case BANNER = 'banner';
    case MAIN = 'main-slide';

    public static function toArray()
    {
        return [
            self::BANNER => 'banner',
            self::MAIN => 'main-slide'
        ];
    }
}
