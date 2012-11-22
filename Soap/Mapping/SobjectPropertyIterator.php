<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use Codemitte\Common\Collection\GenericMap;

class SobjectPropertyIterator extends GenericMap
{
    public function __construct(SobjectInterface $sobject)
    {
        parent::__construct($sobject->toArray());
    }
}
