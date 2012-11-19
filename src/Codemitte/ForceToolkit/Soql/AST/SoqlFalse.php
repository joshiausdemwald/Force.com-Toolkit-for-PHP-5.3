<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlFalse extends SoqlValue
{
    public function __construct()
    {
        $this->value = 'FALSE';
    }
}
