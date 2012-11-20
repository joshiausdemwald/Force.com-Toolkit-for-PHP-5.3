<?php
namespace Codemitte\ForceToolkit\Metadata\Cache;

use
    Symfony\Component\Locale\Locale,
    Codemitte\Common\Cache\GenericCachedInterface,
    Codemitte\Common\Cache\AbstractCache
;

class MetadataFileCache extends AbstractCache implements MetadataCacheInterface
{
    /**
     * @var string
     */
    protected $cacheDirectory;

    /**
     * @var array
     */
    protected $store = array();

    /**
     * Constructor.
     *
     * @todo bind cache dir creation to cache warmup event.
     * @param $cacheDirectory
     * @param $ttl
     */
    public function __construct($cacheDirectory, $ttl = -1)
    {
        $this->setTTL($ttl);

        $this->cacheDirectory = $cacheDirectory . '/' . Locale::getDefault();

        if( ! is_dir($this->cacheDirectory))
        {
            $umask = umask(0000);

            mkdir($this->cacheDirectory, 0777, true);

            umask($umask);
        }
    }

    /**
     * @abstract
     * @param $key
     * @return GenericCachedInterface
     */
    public function get($key)
    {
        $pathname = $this->getPathname($key);

        if(isset($this->store[$pathname]))
        {
            return $this->store[$pathname];
        }
        return $this->store[$pathname] = unserialize(file_get_contents($pathname));
    }

    /**
     * @abstract
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $pathname = $this->getPathname($key);

        return isset($this->store[$pathname]) || file_exists($pathname);
    }


    /**
     * @abstract
     * @param $key
     * @return void
     */
    public function remove($key)
    {
        $pathname = $this->getPathname($key);

        if(isset($this->store[$pathname]))
        {
            unset($this->store[$pathname]);
        }
        unlink($pathname);
    }

    /**
     * @abstract
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        $pathname = $this->getPathname($key);

        $this->store[$pathname] = $value;

        file_put_contents($pathname, serialize($value));
    }

    /**
     * @param $key
     * @return string
     */
    protected function getPathname($key)
    {
        return $this->cacheDirectory . '/' . $key;
    }
}
