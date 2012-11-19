<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlSelectFunction extends AbstractSelectable
{
    protected $argument;

    protected $name;

    /**
     * @param $name
     * @param $argument
     */
    public function __construct($name, $argument)
    {
        $this->name = $name;

        $this->argument = $argument;
    }

    public function __toString()
    {
        return $this->name . '(' . $this->argument . ')';
    }
}
