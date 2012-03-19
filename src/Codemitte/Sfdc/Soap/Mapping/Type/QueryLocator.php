<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

/**
 * QueryLocator
 */
class QueryLocator extends GenericType
{
    static function getName()
    {
        return 'QueryLocator';
    }

    public static function toXml($value)
    {
        // TODO: Implement toXml() method.
    }

    public static function fromXml($value)
    {
        // TODO: Implement fromXml() method.
    }

    /**
     * The target namespace of the type.
     *
     * @return string
     */
    public static function getURI()
    {
        return 'urn:enterprise.soap.sforce.com';
    }
}
