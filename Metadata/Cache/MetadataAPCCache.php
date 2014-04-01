<?php
namespace Codemitte\ForceToolkit\Metadata\Cache;

use
    Symfony\Component\Locale\Locale,
    Codemitte\Common\Cache\GenericCachedInterface,
    Codemitte\Common\Cache\AbstractCache
;

class MetadataAPCCache extends AbstractCache implements MetadataCacheInterface
{
    /**
     * Constructor.
     *
     * @param $ttl
     */
    public function __construct($ttl = -1)
    {
        $this->setTTL($ttl);
    }

    /**
     * @abstract
     * @param $key
     * @return GenericCachedInterface
     */
    public function get($key)
    {
        return \apc_fetch('sfdcdescriberesult_' . $key);
    }

    /**
     * @abstract
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return \apc_exists('sfdcdescriberesult_' . $key);
    }


    /**
     * @abstract
     * @param $key
     * @return void
     */
    public function remove($key)
    {
        return \apc_delete('sfdcdescriberesult_' . $key);
    }

    /**
     * @abstract
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        \apc_store('sfdcdescriberesult_'. $key, $value);
    }
}
