<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class QueryResult implements ClassInterface
{
    /**
     * @var boolean $done
     */
    private $done;

    /**
     * @var QueryLocator $queryLocator
     */
    private $queryLocator;

    /**
     * @var sObject $records
     */
    private $records;

    /**
     * @var int $size
     */
    private $size;

    /**
     *
     * @param boolean $done
     * @param QueryLocator $queryLocator
     * @param sObject $records
     * @param int $size
     *
     * @access public
     */
    public function __construct($done, $queryLocator, $records, $size)
    {
        $this->done         = $done;
        $this->queryLocator = $queryLocator;
        $this->records      = $records;
        $this->size         = $size;
    }

    /**
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\QueryLocator
     */
    public function getQueryLocator()
    {
        return $this->queryLocator;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\sObject
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

}
