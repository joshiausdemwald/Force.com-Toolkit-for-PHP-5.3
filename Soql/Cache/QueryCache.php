<?php
namespace Codemitte\ForceToolkit\Soql\Cache;

use Codemitte\Common\Cache\GenericCacheInterface;

class QueryCache implements GenericCacheInterface
{
    private $ttl;

    /**
     * @param $key
     * @return GenericCachedInterface
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }

    /**
     * @param $key
     * @return void
     */
    public function remove($key)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * @param $key
     * @param string $timestamp
     * @return bool
     */
    public function isFresh($key, $timestamp = null)
    {
        // TODO: Implement isFresh() method.
    }

    /**
     * @return string
     */
    public function getTTL()
    {
        return $this->ttl;
    }

    /**
     * @param string $ttl
     * @return void
     */
    public function setTTL($ttl)
    {
        $this->ttl = $ttl;
    }
}
