<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

/**
 * GenericType
 */
abstract class GenericType implements TypeInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * Constructor
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }


    public static function fromXml($value)
    {
        return new static($value);
    }

    public static function toXml($value)
    {
        return '<' . static::getName() . '>' . $value . '</' . static::getName() . '>';
    }

    public function __toString()
    {
        return $this->value;
    }
}
