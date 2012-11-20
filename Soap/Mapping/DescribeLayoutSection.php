<?php
namespace Codemitte\ForceToolkit\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

/**
 * DescribeLayoutSection
 *
 * columns	int	Number of columns in this DescribeLayoutSection.
 * heading	string	Heading text (label) for this DescribeLayoutSection.
 * layoutRows	DescribeLayoutRow[]	Array of one or more DescribeLayoutRow objects.
 * rows	int	Number of rows in this DescribeLayoutSection.
 * useCollapsibleSection	boolean	Indicates whether this DescribeLayoutSection is a collapsible section,
 * also known as a “twistie” (true), or not (false).
 * useHeading	boolean	Indicates whether to use the heading (true) or not (false).
 */
class DescribeLayoutSection implements ClassInterface
{
    /**
     *
     * @var int $columns
     */
    private $columns;

    /**
     *
     * @var string $heading
     */
    private $heading;

    /**
     *
     * @var DescribeLayoutRow $layoutRows
     */
    private $layoutRows;

    /**
     *
     * @var int $rows
     */
    private $rows;

    /**
     *
     * @var boolean $useCollapsibleSection
     */
    private $useCollapsibleSection;

    /**
     *
     * @var boolean $useHeading
     */
    private $useHeading;

    /**
     *
     * @param int $columns
     * @param string $heading
     * @param DescribeLayoutRow $layoutRows
     * @param int $rows
     * @param boolean $useCollapsibleSection
     * @param boolean $useHeading
     *
     * @access public
     */
    public function __construct($columns, $heading, $layoutRows, $rows, $useCollapsibleSection, $useHeading)
    {
        $this->columns               = $columns;
        $this->heading               = $heading;
        $this->layoutRows            = $layoutRows;
        $this->rows                  = $rows;
        $this->useCollapsibleSection = $useCollapsibleSection;
        $this->useHeading            = $useHeading;
    }

    /**
     * @return int
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutRow
     */
    public function getLayoutRows()
    {
        return $this->layoutRows;
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return boolean
     */
    public function getUseCollapsibleSection()
    {
        return $this->useCollapsibleSection;
    }

    /**
     * @return boolean
     */
    public function getUseHeading()
    {
        return $this->useHeading;
    }

}
