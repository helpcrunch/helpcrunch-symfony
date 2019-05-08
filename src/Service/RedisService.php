<?php

namespace Helpcrunch\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RedisService
{
    const ADMIN_TOKEN_TTL = 86400;

    private $container;

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
        $this->container = $container;
        $this->ttl = $container->getParameter('redis_ttl');

        $this->redis = new \Redis();
        $this->connect();
    }

    public function connect(string $host = null, int $port = null): bool
    {
        return $this->redis->connect(
            $host ?? $this->container->getParameter('redis_host'),
            $port ?? $this->container->getParameter('redis_port')
        );
    }

    public function pushData($key, $data, int $ttl = null): void
    {
        if ($ttl) {
            $this->redis->setex($key, $ttl, $data);
        } else {
            $this->pushDataWithoutExpiration($key, $data);
            $this->redis->persist($key);
        }
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

    /**
     * @param $key
     * @return null|int
     */
    public function getTimeToLive($key)
    {
        return $this->redis->ttl($key) ?? null;
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

    public function getAllKeys(): array
    {
        return $this->redis->keys('*');
    }

    public function getOrganizationKeys(string $organizationDomain): array
    {
        return $this->redis->keys($organizationDomain . '_*');
    }

    public function increment(string $key): void
    {
        $this->redis->incr($key);
    }

    public function decrement(string $key): void
    {
        $this->redis->decr($key);
    }
}
