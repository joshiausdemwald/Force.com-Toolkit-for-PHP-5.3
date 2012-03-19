<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class RecordTypeInfo implements ClassInterface
{

    /**
     *
     * @var boolean $available
     */
    private $available;

    /**
     *
     * @var boolean $defaultRecordTypeMapping
     */
    private $defaultRecordTypeMapping;

    /**
     *
     * @var string $name
     */
    private $name;

    /**
     *
     * @var ID $recordTypeId
     */
    private $recordTypeId;

    /**
     *
     * @param boolean $available
     * @param boolean $defaultRecordTypeMapping
     * @param string $name
     * @param ID $recordTypeId
     *
     * @access public
     */
    public function __construct($available, $defaultRecordTypeMapping, $name, $recordTypeId)
    {
        $this->available                = $available;
        $this->defaultRecordTypeMapping = $defaultRecordTypeMapping;
        $this->name                     = $name;
        $this->recordTypeId             = $recordTypeId;
    }

    /**
     * @return boolean
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @return boolean
     */
    public function getDefaultRecordTypeMapping()
    {
        return $this->defaultRecordTypeMapping;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\ID
     */
    public function getRecordTypeId()
    {
        return $this->recordTypeId;
    }

}
