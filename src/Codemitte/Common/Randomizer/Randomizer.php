<?php
namespace Codemitte\Common\Randomizer;

class Randomizer implements RandomizerInterface
{
    /**
     * Returns a generated, unique Id that is encoded
     * base64, if argument isset to true.
     *
     * @param bool $encode_base_64
     * @return mixed
     */
    public function getUniqueId($encode_base_64 = true)
    {
        $id = uniqid(mt_rand(), true);

        return $encode_base_64 ? base64_encode($id) : $id;
    }
}
