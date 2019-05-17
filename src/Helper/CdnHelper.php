<?php

namespace Helpcrunch\Helper;

class CdnHelper
{
    const BASE_URL = 'https://ucarecdn.com/';
    const PROXY_BASE_URL = 'https://helpcrunch.ucr.io/';

    public static function getUrl(string $imageCdnKey = null, array $options = []): string
    {
        if (!$imageCdnKey) {
            return '';
        }
        if (self::isCloudinaryImage($imageCdnKey)) {
            return self::getUrlProxyFromCloudinary($imageCdnKey, $options);
        }

        return self::BASE_URL . $imageCdnKey . '/' . self::getOptionsUrl($options);
    }

    private static function isCloudinaryImage(string $imageCdnKey): bool
    {
        return strpos($imageCdnKey, '-') === false;
    }

    private static function getUrlProxyFromCloudinary(string $imageCdnKey, array $options): string
    {
        $cloudinaryUrl = CloudinaryCdnHelper::getUrl($imageCdnKey);

        return self::PROXY_BASE_URL . self::getOptionsUrl($options) . $cloudinaryUrl;
    }

    public static function getOptionsUrl(array $options): string
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
