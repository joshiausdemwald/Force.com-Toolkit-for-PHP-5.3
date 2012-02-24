<?php
namespace Codemitte\Sfdc\Soap\Mapping;

class DescribeLayoutButton
{

    /**
     * @var boolean $custom
     */
    private $custom;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var string $name
     */
    private $name;

    /**
     *
     * @param boolean $custom
     * @param string $label
     * @param string $name
     *
     * @access public
     */
    public function __construct($custom, $label, $name)
    {
        $this->custom = $custom;
        $this->label  = $label;
        $this->name   = $name;
    }

    /**
     * @return boolean
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
