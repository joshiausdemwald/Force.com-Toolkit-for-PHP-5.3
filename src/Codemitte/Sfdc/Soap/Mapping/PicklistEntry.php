<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class PicklistEntry implements ClassInterface
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

}
