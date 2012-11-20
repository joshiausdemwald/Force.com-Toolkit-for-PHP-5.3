<?php
namespace Codemitte\ForceToolkit\Metadata\Cache;

use
    Symfony\Component\Locale\Locale,
    Codemitte\Common\Cache\GenericCachedInterface,
    Codemitte\Common\Cache\AbstractCache
;

class MetadataArrayCache extends AbstractCache implements MetadataCacheInterface
{
    /**
     * @var array
     */
    protected $store = array();

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
        return $this->store[$this->getPathname($key)];
    }

    /**
     * @abstract
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $pathname = $this->getPathname($key);

        return isset($this->store[$pathname]);
    }


    /**
     * @abstract
     * @param $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->store[$this->getPathname($key)]);
    }

    /**
     * @abstract
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->store[$this->getPathname($key)] = $value;
    }

    /**
     * @param $key
     * @return string
     */
    protected function getPathname($key)
    {
        return Locale::getDefault() . '/' . $key;
    }
}
