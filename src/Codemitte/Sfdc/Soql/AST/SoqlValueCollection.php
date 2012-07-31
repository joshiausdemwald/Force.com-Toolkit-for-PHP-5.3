<?php
namespace Codemitte\Sfdc\Soql\AST;

class SoqlValueCollection extends SoqlValue
{
    public function __construct()
    {
        $this->value = array();
    }

    public function addValue(SoqlValue $value)
    {
        $this->values = $value;
    }
}
