<?php

namespace Helpcrunch\Helper;

class CloudinaryCdnHelper
{
    const BASE_URL = 'https://res.cloudinary.com/helpcrunch/';

    public static function getUrl(string $name):string
    {
        return self::BASE_URL . $name;
    }
}
