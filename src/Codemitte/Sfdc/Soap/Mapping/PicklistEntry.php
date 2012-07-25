<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class PicklistEntry implements ClassInterface, \Serializable
{
    /**
     *
     * @var boolean $active
     * @access public
     */
    private $active;

    /**
     *
     * @var boolean $defaultValue
     * @access public
     */
    private $defaultValue;

    /**
     *
     * @var string $label
     * @access public
     */
    private $label;

    /**
     *
     * @var base64Binary $validFor
     * @access public
     */
    private $validFor;

    /**
     *
     * @var string $value
     * @access public
     */
    private $value;

    /**
     *
     * @param boolean $active
     * @param boolean $defaultValue
     * @param string $label
     * @param base64Binary $validFor
     * @param string $value
     *
     * @access public
     */
    public function __construct($active, $defaultValue, $label, $validFor, $value)
    {
        $this->active       = $active;
        $this->defaultValue = $defaultValue;
        $this->label        = $label;
        $this->validFor     = $validFor;
        $this->value        = $value;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return boolean
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\base64Binary
     * @see http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describesobjects_describesobjectresult.htm#i1427864
     */
    public function getValidFor()
    {
        return $this->validFor;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
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
            'active' => $this->active,
            'defaultValue' => $this->defaultValue,
            'label' => $this->label,
            'validFor' => $this->validFor,
            'value' => $this->value
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

        $this->active =  $data['active'];
        $this->defaultValue =  $data['defaultValue'];
        $this->label=  $data['label'];
        $this->validFor=  $data['validFor'];
        $this->value=  $data['value'];
    }
}
