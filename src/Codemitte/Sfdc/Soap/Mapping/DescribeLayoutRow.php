<?php
namespace Codemitte\Sfdc\Soap\Mapping;

class DescribeLayoutRow
{

  /**
   * 
   * @var DescribeLayoutItem $layoutItems
   * @access public
   */
  public $layoutItems;

  /**
   * 
   * @var int $numItems
   * @access public
   */
  public $numItems;

  /**
   * 
   * @param DescribeLayoutItem $layoutItems
   * @param int $numItems
   * @access public
   */
  public function __construct($layoutItems, $numItems)
  {
    $this->layoutItems = $layoutItems;
    $this->numItems = $numItems;
  }

}
