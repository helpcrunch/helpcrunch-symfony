<?php

namespace Helpcrunch\Service;

use Symfony\Component\HttpFoundation\Request;

class TokenValidationService
{
    /**
     * @var RedisService $redis
     */
    private $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function validateToken(Request $request): bool
    {
        $authHeader = $request->headers->get('Authorization');

        $isDeviceAuth = preg_match(
            '/^Bearer device="(?P<device>\S+?)"\s+secret="(?P<secret>\S+?)"$/i',
            $authHeader,
            $matches
        );
        if ($isDeviceAuth) {
            return $this->redis->getData($matches['secret']) == $matches['device'];
        }

        $isTokenAuth = preg_match(
            '/^Bearer admin="(?P<admin>\S+?)" token="(?P<token>\S+?)"$/i',
            $authHeader,
            $matches
        );
        if ($isTokenAuth) {
            return $this->redis->getData($matches['token']) == $matches['admin'];
        }

        $isHelpcrunchAuth = preg_match(
            '/^Bearer helpcrunch-key="(?P<helpcrunchKey>\S+?)"$/i', $authHeader, $matches
        );
        if ($isHelpcrunchAuth) {
            return (bool) $this->redis->getData($matches['helpcrunchKey']);
        }

        return false;
    }
}
