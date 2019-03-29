<?php

namespace Helpcrunch\Helper;

class CdnHelper
{
    const BASE_URL = 'https://ucarecdn.com/';

    public static function getUrl(string $imageCdnKey, array $options = []):string
    {
        return self::BASE_URL . $imageCdnKey . '/' . self::getOptionsUrl($options);
    }

    public static function getOptionsUrl(array $options):string
    {
        $urlParts = ['-'];
        foreach ($options as $optionKey => $optionValue) {
            $urlParts[] = $optionKey;
            $urlParts[] = $optionValue;
        }

        $url = '';
        if (count($urlParts) > 1) {
            $url = implode('/', $urlParts) . '/';
        }

        return $url;
    }
}
