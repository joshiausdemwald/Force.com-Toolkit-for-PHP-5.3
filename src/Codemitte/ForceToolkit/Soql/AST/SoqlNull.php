<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlNull extends SoqlValue
{
    public function __construct()
    {
        $this->value = 'NULL';
    }
}
