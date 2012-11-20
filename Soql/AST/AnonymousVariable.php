<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class AnonymousVariable extends SoqlValue
{
    public function __construct()
    {
        parent::__construct('?');
    }
}
