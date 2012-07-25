<?php
namespace Codemitte\Common\Cache;

interface GenericCachedInterface
{
    /**
     * @abstract
     * @param string/int $timestamp
     * @return bool
     */
    public function isFresh($timestamp);
}
