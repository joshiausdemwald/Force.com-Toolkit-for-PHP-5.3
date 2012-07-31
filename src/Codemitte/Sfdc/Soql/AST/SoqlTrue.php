<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlTrue extends SoqlValue
{
    public function __construct()
    {
        $this->value = 'TRUE';
    }
}
