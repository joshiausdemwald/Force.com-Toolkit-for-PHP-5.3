<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class RecordTypeMapping implements ClassInterface
{
    /**
     *
     * @var boolean $available
     * @access private
     */
    private $available;

    /**
     *
     * @var boolean $defaultRecordTypeMapping
     * @access private
     */
    private $defaultRecordTypeMapping;

    /**
     *
     * @var ID $layoutId
     * @access private
     */
    private $layoutId;

    /**
     *
     * @var string $name
     * @access private
     */
    private $name;

    /**
     *
     * @var PicklistForRecordType $picklistsForRecordType
     * @access private
     */
    private $picklistsForRecordType;

    /**
     *
     * @var ID $recordTypeId
     * @access private
     */
    private $recordTypeId;

    /**
     *
     * @param boolean $available
     * @param boolean $defaultRecordTypeMapping
     * @param ID $layoutId
     * @param string $name
     * @param PicklistForRecordType $picklistsForRecordType
     * @param ID $recordTypeId
     * @access private
     */
    public function __construct($available, $defaultRecordTypeMapping, $layoutId, $name, $picklistsForRecordType, $recordTypeId)
    {
        $this->available = $available;
        $this->defaultRecordTypeMapping = $defaultRecordTypeMapping;
        $this->layoutId = $layoutId;
        $this->name = $name;
        $this->picklistsForRecordType = $picklistsForRecordType;
        $this->recordTypeId = $recordTypeId;
    }

    /**
     * @param boolean $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }

    /**
     * @return boolean
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param boolean $defaultRecordTypeMapping
     */
    public function setDefaultRecordTypeMapping($defaultRecordTypeMapping)
    {
        $this->defaultRecordTypeMapping = $defaultRecordTypeMapping;
    }

    /**
     * @return boolean
     */
    public function getDefaultRecordTypeMapping()
    {
        return $this->defaultRecordTypeMapping;
    }

    /**
     * @param \Codemitte\Sfdc\Soap\Mapping\ID $layoutId
     */
    public function setLayoutId($layoutId)
    {
        $this->layoutId = $layoutId;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\ID
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Codemitte\Sfdc\Soap\Mapping\PicklistForRecordType $picklistsForRecordType
     */
    public function setPicklistsForRecordType($picklistsForRecordType)
    {
        $this->picklistsForRecordType = $picklistsForRecordType;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\PicklistForRecordType
     */
    public function getPicklistsForRecordType()
    {
        return $this->picklistsForRecordType;
    }

    /**
     * @param \Codemitte\Sfdc\Soap\Mapping\ID $recordTypeId
     */
    public function setRecordTypeId($recordTypeId)
    {
        $this->recordTypeId = $recordTypeId;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\ID
     */
    public function getRecordTypeId()
    {
        return $this->recordTypeId;
    }


}
