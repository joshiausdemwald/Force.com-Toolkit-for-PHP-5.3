<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlNull extends SoqlValue
{
    public function __construct()
    {
        $this->value = 'NULL';
    }
}
