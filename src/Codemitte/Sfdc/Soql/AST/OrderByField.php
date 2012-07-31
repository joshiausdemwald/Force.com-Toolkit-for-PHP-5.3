<?php
namespace Codemitte\Sfdc\Soql\AST;

class OrderByField implements SortableInterface
{
    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    const NULLS_FIRST = 'NULLS FIRST';
    const NULLS_LAST = 'NULLS LAST';

    private $name;

    private $direction = self::DIRECTION_ASC;

    private $nulls;

    public function __construct($name)
    {
        $this->name = $name;
    }

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
}
