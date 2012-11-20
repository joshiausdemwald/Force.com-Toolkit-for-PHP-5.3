<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlTrue extends SoqlValue
{
    public function __construct()
    {
        $this->value = 'TRUE';
    }
}
