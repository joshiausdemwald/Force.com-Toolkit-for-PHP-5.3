<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

/**
 * QueryLocator
 */
class fieldType extends GenericType
{
    const string = 'string';
    const picklist = 'picklist';
    const multipicklist = 'multipicklist';
    const combobox = 'combobox';
    const reference = 'reference';
    const base64 = 'base64';
    const boolean = 'boolean';
    const currency = 'currency';
    const textarea = 'textarea';
    const int = 'int';
    const double = 'double';
    const percent = 'percent';
    const phone = 'phone';
    const id = 'id';
    const date = 'date';
    const datetime = 'datetime';
    const time = 'time';
    const url = 'url';
    const email = 'email';
    const encryptedstring = 'encryptedstring';
    const datacategorygroupreference = 'datacategorygroupreference';
    const anyType = 'anyType';

    static function getName()
    {
        return 'fieldType';
    }
}
