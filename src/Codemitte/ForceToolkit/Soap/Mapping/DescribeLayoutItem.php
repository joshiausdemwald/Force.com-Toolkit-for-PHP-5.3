<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class DescribeLayoutItem implements ClassInterface
{

    /**
     *
     * @var boolean $editable
     */
    private $editable;

    /**
     *
     * @var string $label
     */
    private $label;

    /**
     *
     * @var DescribeLayoutComponent $layoutComponents
     * @access public
     */
    private $layoutComponents;

    /**
     *
     * @var boolean $placeholder
     */
    private $placeholder;

    /**
     *
     * @var boolean $required
     */
    private $required;

    /**
     *
     * @param boolean $editable
     * @param string $label
     * @param DescribeLayoutComponent $layoutComponents
     * @param boolean $placeholder
     * @param boolean $required
     *
     * @access public
     */
    public function __construct($editable, $label, $layoutComponents, $placeholder, $required)
    {
        $this->editable         = $editable;
        $this->label            = $label;
        $this->layoutComponents = $layoutComponents;
        $this->placeholder      = $placeholder;
        $this->required         = $required;
    }

    /**
     * @return boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutComponent
     */
    public function getLayoutComponents()
    {
        return $this->layoutComponents;
    }

    /**
     * @return boolean
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

}
