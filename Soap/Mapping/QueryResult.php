<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use
    Codemitte\Soap\Mapping\ClassInterface,
    Codemitte\Soap\Mapping\GenericResultCollection
;

class QueryResult implements ClassInterface
{
    /**
     * @var string
     */
    private $queryLocator;

    /**
     * @var bool
     */
    private $done;

    /**
     * @var array
     */
    private $records;

    /**
     * @var int
     */
    private $size;

    /**
     * @return string
     */
    public function getQueryLocator()
    {
        return $this->queryLocator;
    }

    /**
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    public function getRecords()
    {
        return new GenericResultCollection($this->records);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}
