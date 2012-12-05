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
}
