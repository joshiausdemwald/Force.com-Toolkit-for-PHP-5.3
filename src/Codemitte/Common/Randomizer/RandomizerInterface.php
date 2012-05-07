<?php
namespace Codemitte\Common\Randomizer;

interface RandomizerInterface
{
    /**
     * Returns a generated, unique Id that is encoded
     * base64, if argument isset to true.
     *
     * @abstract
     * @param bool $encode_base_64
     * @return mixed
     */
    public function getUniqueId($encode_base_64 = true);
}
