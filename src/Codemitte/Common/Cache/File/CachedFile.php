<?php
namespace Codemitte\Common\Cache\File;

use
    \SplFileInfo,
    Codemitte\Common\Cache\GenericCachedInterface;

class CachedFile extends SplFileInfo implements GenericCachedInterface
{
    /**
     * @param null $timestamp
     * @return bool
     */
    public function isFresh($timestamp)
    {
        return $this->getCTime() < $timestamp;
    }
}
