<?php
namespace Codemitte\Common\Cache\File;

use Codemitte\Common\Cache\AbstractCache;
use Codemitte\Common\Cache\GenericCachedInterface;

class FileCache extends AbstractCache implements FileCacheInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var boolean
     */
    private $isAllowXSendfile;

    /**
     * @var array
     */
    private $normalizedPaths = array();

    /**
     * Constructor.
     *
     * @param $cacheDir
     * @param $ttl
     * @param bool $isAllowXSendfile
     * @return \Codemitte\Common\Cache\File\FileCache
     */
    public function __construct($cacheDir, $ttl, $isAllowXSendfile = false)
    {
        $this->cacheDir = $cacheDir;

        $this->setTTL($ttl);

        $this->isAllowXSendfile($isAllowXSendfile);
    }

    /**
     * @param $pathname
     * @return GenericCachedInterface
     */
    public function get($pathname)
    {
        return new CachedFile($this->normalizePath($pathname));
    }

    /**
     * @param $pathname
     * @return bool
     */
    public function has($pathname)
    {
        return file_exists($this->normalizePath($pathname));
    }

    /**
     * @param $pathname
     * @internal param $path
     * @return void
     */
    public function remove($pathname)
    {
        unlink($this->normalizePath($pathname));
    }

    /**
     * @param $pathname
     * @param $data
     * @return void
     */
    public function set($pathname, $data)
    {
        $normalized_pathname = $this->normalizePath($pathname);

        $dirname = dirname($normalized_pathname);

        if( ! is_dir($dirname))
        {
            $umask = umask(0000);
            mkdir($dirname, 0777, true);
            umask($umask);
        }

        file_put_contents($normalized_pathname, $data);

        chmod($normalized_pathname, 0777);
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * isAllowXSendfile()
     *
     * @param null $isAllowXSendfile
     * @internal param bool|null $allowXSendfile
     * @return bool
     */
    public function isAllowXSendfile($isAllowXSendfile = null)
    {
        if(null === $isAllowXSendfile)
        {
            return $this->isAllowXSendfile;
        }
        else
        {
            $this->isAllowXSendfile = $isAllowXSendfile;
        }
    }

    /**
     * Normalizes a path, by stripping trailing slashes.
     *
     * @param $pathname
     * @return string $normalizedPath
     */
    protected function normalizePath($pathname)
    {
        if( ! isset($this->normalizedPaths[$pathname]))
        {
            $pathname = (string)$pathname;

            $pathinfo = pathinfo($pathname);

            // including extension
            $basename = $pathinfo['basename'];
            $dirname  = $pathinfo['dirname'];

            $hash = hash('sha1', $dirname);

            $cache_dir = substr($hash, 0, 2) . '/' . substr($hash, 2, 2). '/' . $hash;

            $this->normalizedPaths[$pathname] = $this->cacheDir . '/' . $cache_dir . '/' . $basename;
        }
        return $this->normalizedPaths[$pathname];
    }
}