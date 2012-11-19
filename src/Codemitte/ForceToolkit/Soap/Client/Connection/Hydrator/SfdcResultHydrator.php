<?php
namespace Codemitte\ForceToolkit\Soap\Client\Connection\Hydrator;

use
    Codemitte\Soap\Hydrator\ResultHydrator;

class SfdcResultHydrator extends ResultHydrator
{
    /**
     * Regard duplicate Id attributes that come as a list, transform into
     * single result.
     *
     * @param array $list
     * @param string|null $parentKey
     * @return mixed
     */
    public function doHydrateList(array $list, $parentKey = null)
    {
        $retVal = parent::doHydrateList($list, $parentKey);

        if('Id' === $parentKey)
        {
            if($retVal->count() > 0)
            {
                return $retVal->get(0);
            }
            return null;
        }
        return $retVal;
    }
}
