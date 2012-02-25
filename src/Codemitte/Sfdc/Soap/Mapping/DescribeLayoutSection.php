<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

class DescribeLayoutSection implements ClassInterface
{
  /**
   * 
   * @var int $columns
   * @access public
   */
  public $columns;

  /**
   * 
   * @var string $heading
   * @access public
   */
  public $heading;

  /**
   * 
   * @var DescribeLayoutRow $layoutRows
   * @access public
   */
  public $layoutRows;

  /**
   * 
   * @var int $rows
   * @access public
   */
  public $rows;

  /**
   * 
   * @var boolean $useCollapsibleSection
   * @access public
   */
  public $useCollapsibleSection;

  /**
   * 
   * @var boolean $useHeading
   * @access public
   */
  public $useHeading;

  /**
   * 
   * @param int $columns
   * @param string $heading
   * @param DescribeLayoutRow $layoutRows
   * @param int $rows
   * @param boolean $useCollapsibleSection
   * @param boolean $useHeading
   * @access public
   */
  public function __construct($columns, $heading, $layoutRows, $rows, $useCollapsibleSection, $useHeading)
  {
    $this->columns = $columns;
    $this->heading = $heading;
    $this->layoutRows = $layoutRows;
    $this->rows = $rows;
    $this->useCollapsibleSection = $useCollapsibleSection;
    $this->useHeading = $useHeading;
  }
}
