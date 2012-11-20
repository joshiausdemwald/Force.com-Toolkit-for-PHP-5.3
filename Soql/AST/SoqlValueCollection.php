<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlValueCollection extends SoqlValue
{
    public function __construct()
    {
        $this->value = array();
    }

    public function addValue(SoqlValue $value)
    {
        $this->value[] = $value;
    }

    public function __toString()
    {
        $vals = array();

        foreach($this->value AS $v)
        {
            $vals[] = $v;
        }

        return '(' . implode(', ', $vals) . ')';
    }
}
