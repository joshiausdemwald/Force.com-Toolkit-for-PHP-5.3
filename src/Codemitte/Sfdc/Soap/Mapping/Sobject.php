<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\GenericResult;

class Sobject extends GenericResult implements SobjectInterface
{
    /**
     * @var \Codemitte\Sfdc\Soap\Mapping\Type\ID
     */
    private $Id;

    /**
     * @var string
     */
    private $sObjectType;

    /**
     * Constructor.
     *
     * @override
     *
     * @param array $values
     *
     * @internal param $sobjectType
     */
    public function __construct($sObjectType, $values = array())
    {
        parent::__construct($values);

        $this->setSobjectType($sObjectType);
    }

    /**
     * Returns the NULL fields to (re-)set to NULL
     *
     * <element name="fieldsToNull"
     * type="xsd:string"
     * nillable="true"
     * minOccurs="0"
     * maxOccurs="unbounded"/>
     *
     * Array of one or more field names whose value you want to explicitly set to null.
     * When used with update() or upsert(), you can specify only those fields that you
     * can update and that have the nillable property. When used with create(), you can
     * specify only those fields that you can create and that have the nillable or the
     * default on create property.
     *
     * For example, if specifying an ID field or required field results in a run-time error,
     * you can specify that field name in fieldsToNull. Similarly, if you need to set a
     * picklist value to none when creating a record, but the picklist has a default value,
     * you can specify the field in fieldsToNull.
     *
     * @return array|null $fieldsToNull
     */
    public function getFieldsToNull()
    {
        $retVal = array();

        foreach($this->getKeys() AS $key)
        {
            $value = $this[$key];

            if(null === $value || '' === $value)
            {
                $retVal[] = $key;
            }
        }
        return count($retVal) > 0 ? $retVal : null;
    }

    /**
     * getId()
     *
     * @return \ID
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Returns the sobject type.
     *
     *
     * @return string
     */
    public function getSobjectType()
    {
        return $this->sObjectType;
    }

    /**
     * Sets the sobject type.
     *
     * @param string sObjectType
     */
    public function setSobjectType($sObjectType)
    {
        $this->sObjectType = $sObjectType;
    }
}