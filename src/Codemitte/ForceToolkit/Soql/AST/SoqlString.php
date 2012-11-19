<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlString extends SoqlValue
{
    public function __toString()
    {
        $v = $this->getValue();
        $v = trim($v, '"\'');
        return '\'' . $v  . '\'';
    }
}
