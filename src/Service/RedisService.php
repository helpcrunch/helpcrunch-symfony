<?php

namespace Helpcrunch\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RedisService
{
    const ADMIN_TOKEN_KEY = 'adminToken_';
    const SESSION_DATA_KEY = 'sessionData_';
    const KNOWN_USER_SESSIONS_KEY = 'knownSessions_';

    const ADMIN_TOKEN_TTL = 86400;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var int
     */
    private $ttl;

    public function __construct(ContainerInterface $container)
    {
        $this->redis = new \Redis();
        $this->redis->connect($container->getParameter('redis_host'), $container->getParameter('redis_port'));

        $this->ttl = $container->getParameter('redis_ttl');
    }

    public function pushData($key, $data, int $ttl = null): void
    {
        $this->redis->setex($key, $ttl ?? $this->ttl, $data);
    }

    public function pushDataWithoutExpiration($key, $data): void
    {
        $this->redis->set($key, $data);
    }

    public function delete($key): void
    {
        $this->redis->delete($key);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function getData($key)
    {
        return $this->redis->get($key);
    }

    public function pushArrayData($key, $data): void
    {
        $this->redis->hMset($key, $data);
    }

    public function getArrayData(string $key): array
    {
        return $this->redis->hGetAll($key);
    }

    public function deleteArrayData(string $key, $field)
    {
        $this->redis->hDel($key, $field);
    }

    public function pushList($key, $value): void
    {
        $this->redis->lPush($key, $value);
    }

    public function getList($key): array
    {
        return $this->redis->lRange($key, 0, -1);
    }

    public function removeFromList($key, $value): void
    {
        $this->redis->lRem($key, $value, 0);
    }
}
