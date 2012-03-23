<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\ClassInterface;

class DescribeLayoutRow implements ClassInterface
{
    /**
     *
     * @var DescribeLayoutItem $layoutItems
     */
    private $layoutItems;

    /**
     *
     * @var int $numItems
     */
    private $numItems;

    /**
     *
     * @param DescribeLayoutItem $layoutItems
     * @param int $numItems
     *
     * @access public
     */
    public function __construct($layoutItems, $numItems)
    {
        $this->layoutItems = $layoutItems;
        $this->numItems    = $numItems;
    }

    /**
     * @return int
     */
    public function getNumItems()
    {
        return $this->numItems;
    }

    /**
     * @return array<\Codemitte\Sfdc\Soap\Mapping\DescribeLayoutItem>
     */
    public function getLayoutItems()
    {
        return $this->layoutItems;
    }

}
