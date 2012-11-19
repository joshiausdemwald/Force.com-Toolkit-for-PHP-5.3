<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlOrderByAggregateFunction extends SoqlAggregateFunction implements SortableInterface
{
    private $direction = SortableInterface::DIRECTION_ASC;

    private $nulls;

    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setNulls($nulls)
    {
        $this->nulls = $nulls;
    }

    public function getNulls()
    {
        return $this->nulls;
    }

    public function __toString()
    {
        $retVal = parent::__toString();

        if($dir = $this->getDirection())
        {
            $retVal .= ' ' . $dir;
        }

        if($nulls = $this->getNulls())
        {
            $retVal .=  ' ' . $nulls;
        }

        return $retVal;
    }
}
