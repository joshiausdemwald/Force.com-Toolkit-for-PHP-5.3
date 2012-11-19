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

    /**
     * Creates a random code consisting of digits only
     * (like a pin number)
     *
     * @abstract
     * @param int $length
     * @return string
     */
    public function createRandomCode($length = 4);
}
