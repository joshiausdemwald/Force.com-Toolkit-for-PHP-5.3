<?php
namespace Codemitte\Common\Cache;

abstract class AbstractCache implements GenericCacheInterface
{
    /**
     * @var string $ttl
     */
    protected $ttl;

    /**
     * @param $key
     * @param string $timestamp
     * @return bool
     */
    public function isFresh($key, $timestamp = null)
    {
        if(null === $this->getTTL() || $this->getTTL() < 0)
        {
            return true;
        }
        if(null === $timestamp)
        {
            $timestamp = time();
        }
        return $this->has($key) && $this->get($key)->isFresh($timestamp - $this->getTTL());
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
