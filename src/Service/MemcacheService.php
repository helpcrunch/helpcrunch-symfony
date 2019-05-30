<?php

namespace Helpcrunch\Service;

use \Memcached;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class MemcacheService
{
    /**
     * @var int
     */
    private $ttl = 300;

    /**
     * @var Memcached
     */
    private $memcached;

    public function __construct(string $memcacheConnection)
    {
        $this->memcached = MemcachedAdapter::createConnection($memcacheConnection);
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        $cached = $this->memcached->get($key);
        if (!empty($cached)) {
            $cached = unserialize($cached);
        }

        return $cached;
    }

    /**
     * @param mixed $forCache
     */
    public function set(string $key, $forCache): void
    {
        $this->memcached->set($key, serialize($forCache), $this->ttl);
    }

    public function delete(string $key): void
    {
        $this->memcached->delete($key);
    }

    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }
}
