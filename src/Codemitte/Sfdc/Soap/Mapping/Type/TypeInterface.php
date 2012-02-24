<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

/**
 * TypeInterface
 */
interface TypeInterface
{
    public static function getName();

    public static function toXml($value);

    public static function fromXml($value);

    public function __toString();
}
