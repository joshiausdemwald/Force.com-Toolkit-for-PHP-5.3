<?php
namespace Codemitte\Sfdc\Soql\AST;

abstract class SoqlValue implements ComparableInterface
{
    protected $value;

    /**
     * NULL|FALSE|TRUE|String|Date|DateTime
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
