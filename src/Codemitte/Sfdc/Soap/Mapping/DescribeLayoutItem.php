<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

class DescribeLayoutItem implements ClassInterface
{

  /**
   * 
   * @var boolean $editable
   * @access public
   */
  public $editable;

  /**
   * 
   * @var string $label
   * @access public
   */
  public $label;

  /**
   * 
   * @var DescribeLayoutComponent $layoutComponents
   * @access public
   */
  public $layoutComponents;

  /**
   * 
   * @var boolean $placeholder
   * @access public
   */
  public $placeholder;

  /**
   * 
   * @var boolean $required
   * @access public
   */
  public $required;

  /**
   * 
   * @param boolean $editable
   * @param string $label
   * @param DescribeLayoutComponent $layoutComponents
   * @param boolean $placeholder
   * @param boolean $required
   * @access public
   */
  public function __construct($editable, $label, $layoutComponents, $placeholder, $required)
  {
    $this->editable = $editable;
    $this->label = $label;
    $this->layoutComponents = $layoutComponents;
    $this->placeholder = $placeholder;
    $this->required = $required;
  }

}
