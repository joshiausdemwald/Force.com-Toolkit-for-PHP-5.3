<?php
namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Sfdc\Soap\Mapping\ClassInterface;

class DescribeLayoutComponent implements ClassInterface
{

    /**
     * @var int $displayLines
     */
    private $displayLines;

    /**
     * @var int $tabOrder
     */
    private $tabOrder;

    /**
     * @var layoutComponentType $type
     */
    private $type;

    /**
     * @var string $value
     */
    private $value;

    /**
     *
     * @param int $displayLines
     * @param int $tabOrder
     * @param layoutComponentType $type
     * @param string $value
     *
     * @access public
     */
    public function __construct($displayLines, $tabOrder, $type, $value)
    {
        $this->displayLines = $displayLines;
        $this->tabOrder     = $tabOrder;
        $this->type         = $type;
        $this->value        = $value;
    }

    /**
     * @return int
     */
    public function getDisplayLines()
    {
        return $this->displayLines;
    }

    /**
     * @return int
     */
    public function getTabOrder()
    {
        return $this->tabOrder;
    }

    /**
     * @return \Codemitte\Sfdc\Soap\Mapping\layoutComponentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
