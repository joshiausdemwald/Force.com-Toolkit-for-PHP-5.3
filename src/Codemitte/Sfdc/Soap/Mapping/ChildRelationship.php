<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class ChildRelationship implements ClassInterface
{
    /**
     *
     * @var boolean $cascadeDelete
     */
    private $cascadeDelete;

    /**
     *
     * @var string $childSObject
     */
    private $childSObject;

    /**
     *
     * @var boolean $deprecatedAndHidden
     */
    private $deprecatedAndHidden;

    /**
     *
     * @var string $field
     */
    private $field;

    /**
     *
     * @var string $relationshipName
     */
    private $relationshipName;

    /**
     *
     * @param boolean $cascadeDelete
     * @param string $childSObject
     * @param boolean $deprecatedAndHidden
     * @param string $field
     * @param string $relationshipName
     *
     * @access public
     */
    public function __construct($cascadeDelete, $childSObject, $deprecatedAndHidden, $field, $relationshipName)
    {
        $this->cascadeDelete       = $cascadeDelete;
        $this->childSObject        = $childSObject;
        $this->deprecatedAndHidden = $deprecatedAndHidden;
        $this->field               = $field;
        $this->relationshipName    = $relationshipName;
    }

    /**
     * @return boolean
     */
    public function getCascadeDelete()
    {
        return $this->cascadeDelete;
    }

    /**
     * @return string
     */
    public function getChildSObject()
    {
        return $this->childSObject;
    }

    /**
     * @return boolean
     */
    public function getDeprecatedAndHidden()
    {
        return $this->deprecatedAndHidden;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getRelationshipName()
    {
        return $this->relationshipName;
    }

}