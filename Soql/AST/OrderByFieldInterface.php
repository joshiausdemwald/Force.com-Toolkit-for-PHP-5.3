<?php
namespace Codemitte\ForceToolkit\Soql\AST;

interface OrderByFieldInterface
{
    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    const NULLS_FIRST = 'NULLS FIRST';
    const NULLS_LAST = 'NULLS LAST';

    public function setDirection($direction);

    public function getDirection();

    public function setNulls($nulls);

    public function getNulls();

    public function __toString();
}
