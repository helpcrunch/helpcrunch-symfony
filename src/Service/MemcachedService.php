<?php

namespace Helpcrunch\Service;

use \Memcached;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class MemcachedService
{
    /**
     * @var int
     */
    private $ttl = 300;

    /**
     * @var Memcached
     */
    private $memcached;

    public function __construct(string $memcachedConnection)
    {
        $this->memcached = MemcachedAdapter::createConnection($memcachedConnection);
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        $value = $this->memcached->get($key);
        if (!empty($value)) {
            $value = unserialize($value);
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $this->memcached->set($key, serialize($value), $this->ttl);
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
