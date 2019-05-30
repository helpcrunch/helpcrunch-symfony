<?php

namespace Helpcrunch\Service;

use \Memcached;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class CachedService
{
    /**
     * @var int
     */
    private $memcacheTtl = 300;

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
    public function getFromMemcache(string $memcacheKey)
    {
        $cached = $this->memcached->get($memcacheKey);
        if (!empty($cached)) {
            $cached = unserialize($cached);
        }

        return $cached;
    }

    /**
     * @param mixed $forCache
     */
    public function setToMemcache(string $memcacheKey, $forCache): void
    {
        $this->memcached->set($memcacheKey, serialize($forCache), $this->memcacheTtl);
    }

    public function deleteFromMemcache(string $memcacheKey): void
    {
        $this->memcached->delete($memcacheKey);
    }

    public function setMemcacheTtl(int $memcacheTtl): self
    {
        $this->memcacheTtl = $memcacheTtl;

        return $this;
    }
}
