<?php
namespace Codemitte\ForceToolkit\Metadata;

interface DescribeLayoutFactoryInterface
{
    /**
     * @abstract
     * @param $sobjectType
     * @return \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutResult
     */
    public function getDescribe($sobjectType, array $recordTypeIds = null);
}
