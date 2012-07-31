<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlFalse extends SoqlValue
{
    public function __construct()
    {
        $this->value = 'FALSE';
    }
}
