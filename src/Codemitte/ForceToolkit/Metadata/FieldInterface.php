<?php
namespace Codemitte\ForceToolkit\Metadata;

use Codemitte\ForceToolkit\Soap\Mapping\PicklistEntry;

interface FieldInterface
{
    /**
     * Returns the field's name (e.g. "Account", "eps_Loyalty__c")
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the field's type as one of the
     * TYPE_X-Constants.
     *
     * @abstract
     * @return string
     */
    public function getType();

    /**
     * Returns a list of (aggregated) picklist entries.
     *
     * @abstract
     * @return array
     */
    public function getPicklistEntries();

    /**
     * Returns the orginal field metadata, not filtered (e.g. picklist values)
     * by record type id...
     *
     * @abstract
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Field
     */
    public function getFieldMetadata();

    /**
     * @abstract
     * @return boolean
     */
    public function isRequired();

    /**
     * @abstract
     * @return boolean
     */
    public function isReadonly();

    /**
     * @abstract
     * @return string
     */
    public function getLabel();

    /**
     * For variable-length fields (including binary fields), the maximum size of the field, in bytes.
     * For string fields, the maximum size of the field in Unicode characters (not bytes).
     * For fields of type integer. Maximum number of digits. The API returns an error if an integer value exceeds the
     * number of digits.
     *
     * @abstract
     * @return mixed
     */
    public function getMaxLength();

    /**
     * Returns the name of the controlling field if
     * it's a dependent picklist.
     * @abstract
     * @return string
     */
    public function getControllingFieldName();

    /**
     * @abstract
     * @return bool
     */
    public function isUpdateable();

    /**
     * @abstract
     * @return bool
     */
    public function isCreateable();

    /**
     * Returns true if this field is a dependent
     * picklist.
     *
     * @abstract
     * @return mixed
     */
    public function isDependentPicklist();

    /**
     * For fields of type double. Number of digits to the right of the decimal point. The API silently truncates any
     * extra digits to the right of the decimal point, but it returns a fault response if the number has too many digits
     * to the left of the decimal point.
     *
     * @abstract
     * @return int
     */
    public function getScale();

    /**
     * For fields of type double. Maximum number of digits that can be stored, including all numbers to the left and to
     * the right of the decimal point (but excluding the decimal point character).
     *
     * @abstract
     * @return mixed
     */
    public function getPrecision();

    /**
     * Indicates whether the value must be unique true) or not false).
     *
     * @abstract
     * @return mixed
     */
    public function isUnique();
}

