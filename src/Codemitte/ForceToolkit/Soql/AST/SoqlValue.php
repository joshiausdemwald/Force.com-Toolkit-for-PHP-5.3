<?php
namespace Codemitte\ForceToolkit\Soql\AST;

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

    public function __toString()
    {
        try
        {
            return (string)$this->getValue();
        }
        catch(\Exception $e)
        {
            return (string)$e;
        }
    }
}
