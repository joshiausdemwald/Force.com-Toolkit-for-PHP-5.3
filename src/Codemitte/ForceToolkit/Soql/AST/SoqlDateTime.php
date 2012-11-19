<?php
namespace Codemitte\ForceToolkit\Soql\AST;

class SoqlDateTime extends SoqlDate
{
    public function __toString()
    {
        return $this->value->format(\DateTime::W3C);
    }
}
