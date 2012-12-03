<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class OrderByField extends AbstractOrderByField
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
