<?php
namespace Codemitte\ForceToolkit\Metadata;

interface DescribeSobjectFactoryInterface
{
    /**
     * @abstract
     * @param $sobjectType
     * @return \Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult
     */
    public function getDescribe($sobjectType);
}
