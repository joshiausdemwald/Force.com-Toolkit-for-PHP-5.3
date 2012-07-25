<?php
namespace Codemitte\Common\Cache;

interface GenericCacheInterface
{
    /**
     * @abstract
     * @param $key
     * @return GenericCachedInterface
     */
    public function get($key);

    /**
     * @abstract
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * @abstract
     * @param $key
     * @return void
     */
    public function remove($key);

    /**
     * @abstract
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value);

    /**
     * @abstract
     * @param $key
     * @param string $timestamp
     * @return bool
     */
    public function isFresh($key, $timestamp = null);

    /**
     * @abstract
     * @return string
     */
    public function getTTL();

    /**
     * @abstract
     * @param string $ttl
     * @return void
     */
    public function setTTL($ttl);
}
