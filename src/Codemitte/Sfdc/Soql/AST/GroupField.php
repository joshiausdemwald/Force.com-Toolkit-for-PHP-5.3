<?php
namespace Codemitte\Sfdc\Soql\AST;

class GroupField implements GroupableInterface
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
