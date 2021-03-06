<?php
namespace Codemitte\ForceToolkit\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

class soapType extends GenericType
{
    const tnsID = 'tns:ID';
    const xsdbase64Binary = 'xsd:base64Binary';
    const xsdboolean = 'xsd:boolean';
    const xsddouble = 'xsd:double';
    const xsdint = 'xsd:int';
    const xsdstring = 'xsd:string';
    const xsddate = 'xsd:date';
    const xsddateTime = 'xsd:dateTime';
    const xsdtime = 'xsd:time';
    const xsdanyType = 'xsd:anyType';

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
