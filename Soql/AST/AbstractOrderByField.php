<?php
namespace Codemitte\ForceToolkit\Soql\AST;

abstract class AbstractOrderByField implements OrderByFieldInterface
{
    private $direction = self::DIRECTION_ASC;

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

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        $retVal = $this->getName();

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
