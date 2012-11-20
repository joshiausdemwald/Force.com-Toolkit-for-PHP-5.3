<?php
namespace Codemitte\ForceToolkit\Metadata;

use
    Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry,
    Codemitte\ForceToolkit\Soap\Mapping\Field AS FieldMetadata,
    Codemitte\ForceToolkit\Soap\Mapping\Type\fieldType
;

class Field implements FieldInterface, \Serializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array<PicklistEntry>
     */
    private $picklistEntries;

    /**
     * @var FieldMetadata
     */
    private $fieldMetadata;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $readonly;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $sobjectType;

    /**
     * Constructor.
     *
     * @param string $sobjectType
     * @param $sobjectType
     * @param string $name
     * @param string $type
     * @param string $label
     * @param bool $required
     * @param bool $readonly
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Field $fieldMetadata
     * @param array|null $picklistEntries <PicklistEntry>|null $picklistEntries
     */
    public function __construct($sobjectType, $name, $type, $label, $required, $readonly, FieldMetadata $fieldMetadata, array $picklistEntries = null)
    {
        $this->sobjectType = $sobjectType;

        $this->name = $name;

        $this->type = $type;

        $this->label = $label;

        $this->required = $required;

        $this->readonly = $readonly;

        $this->fieldMetadata = $fieldMetadata;

        $this->picklistEntries = $picklistEntries;
    }

    /**
     * Returns the field's name (e.g. "Account", "eps_Loyalty__c")
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the field's type as one of the
     * TYPE_X-Constants.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns a list of (aggregated) picklist entries.
     *
     * @return array
     */
    public function getPicklistEntries()
    {
        return $this->picklistEntries;
    }

    /**
     * Returns the orginal field metadata, not filtered (e.g. picklist values)
     * by record type id...
     *
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Field
     */
    public function getFieldMetadata()
    {
        return $this->fieldMetadata;
    }

    /**
     * @return boolean
     */
    public function isReadonly()
    {
        return
            $this->readonly ||
            $this->fieldMetadata->getCalculated() ||
            $this->fieldMetadata->getAutoNumber()
        ;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * For variable-length fields (including binary fields), the maximum size of the field, in bytes.
     * For string fields, the maximum size of the field in Unicode characters (not bytes).
     * For fields of type integer. Maximum number of digits. The API returns an error if an integer value exceeds the
     * number of digits.
     *
     * @return int
     */
    public function getMaxLength()
    {
        switch($this->getType())
        {
            case fieldType::anyType:
            case fieldType::email:
            case fieldType::encryptedstring:
            case fieldType::phone:
            case fieldType::string:
            case fieldType::textarea:
            case fieldType::url:
                return $this->fieldMetadata->getLength();

            case fieldType::base64:
                return $this->getByteLength();

            case fieldType::int;
                return $this->fieldMetadata->getDigits();

            case fieldType::currency:
            case fieldType::double:
                return $this->getPrecision() + 1;

        }
        return null;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return
            $this->required ||
            ! $this->fieldMetadata->getNillable();
    }

    /**
     * @return bool
     */
    public function isUpdateable()
    {
        return $this->fieldMetadata->getUpdateable();
    }

    /**
     * @return bool
     */
    public function isCreateable()
    {
        return $this->fieldMetadata->getCreateable();
    }

    /**
     * Returns the name of the controlling field
     * if it's a controlled picklist
     * @return string
     */
    public function getControllingFieldName()
    {
        return $this->fieldMetadata->getControllerName();
    }

    /**
     * Returns true if this field is a dependent picklist.
     *
     * @return bool
     */
    public function isDependentPicklist()
    {
        return $this->fieldMetadata->getDependentPicklist();
    }

    /**
     * For fields of type double. Maximum number of digits that can be stored, including all numbers to the left and to
     * the right of the decimal point (but excluding the decimal point character).
     *
     * @abstract
     * @return mixed
     */
    public function getPrecision()
    {
        return $this->fieldMetadata->getPrecision();
    }


    /**
     * For fields of type double. Number of digits to the right of the decimal point. The API silently truncates any
     * extra digits to the right of the decimal point, but it returns a fault response if the number has too many digits
     * to the left of the decimal point.
     *
     * @abstract
     * @return int
     */
    public function getScale()
    {
        return $this->fieldMetadata->getScale();
    }

    /**
     * Indicates whether the value must be unique true) or not false).
     *
     * @abstract
     * @return mixed
     */
    public function isUnique()
    {
        return $this->fieldMetadata->getUnique();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            'name' => $this->name,
            'type' => $this->type,
            'picklistEntries' => $this->picklistEntries,
            'fieldMetadata' => $this->fieldMetadata,
            'required' => $this->required,
            'readonly' => $this->readonly,
            'label' => $this->label
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->picklistEntries = $data['picklistEntries'];
        $this->fieldMetadata = $data['fieldMetadata'];
        $this->required = $data['required'];
        $this->readonly = $data['readonly'];
        $this->label = $data['label'];
    }

    /**
     * @return string
     */
    public function getSobjectType()
    {
        return $this->sobjectType;
    }

    /**
     * @return boolean
     */
    public function getReadonly()
    {
        return $this->readonly;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }
}

