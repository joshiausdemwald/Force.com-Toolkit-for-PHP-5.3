<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

class layoutComponentType extends GenericType
{
    const Field = 'Field';
    const Separator = 'Separator';
    const SControl = 'SControl';
    const EmptySpace = 'EmptySpace';

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
