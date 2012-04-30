<?php
/**
 * Copyright (C) 2012 code mitte GmbH - Zeughausstr. 28-38 - 50667 Cologne/Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so, subject
 * to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Codemitte\Sfdc\Soap\Mapping;

use Codemitte\Soap\Mapping\GenericResult;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
class Sobject extends GenericResult implements SobjectInterface
{
    /**
     * @var \Codemitte\Sfdc\Soap\Mapping\Type\ID
     */
    private $Id;

    /**
     * @var string
     */
    private $sObjectType;

    /**
     * Constructor.
     *
     * @override
     *
     * @param string $sObjectType
     * @param array $attributes
     */
    public function __construct($sObjectType, $attributes = array())
    {
        parent::__construct($attributes);

        if(array_key_exists('Id', $attributes))
        {
            $this->Id = $attributes['Id'];

            unset($attributes['Id']);
        }

        $this->setSobjectType($sObjectType);
    }

    /**
     * Returns the NULL fields to (re-)set to NULL
     *
     * <element name="fieldsToNull"
     * type="xsd:string"
     * nillable="true"
     * minOccurs="0"
     * maxOccurs="unbounded"/>
     *
     * Array of one or more field names whose value you want to explicitly set to null.
     * When used with update() or upsert(), you can specify only those fields that you
     * can update and that have the nillable property. When used with create(), you can
     * specify only those fields that you can create and that have the nillable or the
     * default on create property.
     *
     * For example, if specifying an ID field or required field results in a run-time error,
     * you can specify that field name in fieldsToNull. Similarly, if you need to set a
     * picklist value to none when creating a record, but the picklist has a default value,
     * you can specify the field in fieldsToNull.
     *
     * @return array|null $fieldsToNull
     */
    public function getFieldsToNull()
    {
        $retVal = array();

        foreach($this->getKeys() AS $key)
        {
            $value = $this[$key];

            if(null === $value || '' === $value)
            {
                $retVal[] = $key;
            }
        }
        return count($retVal) > 0 ? $retVal : null;
    }

    /**
     * getId()
     *
     * @return \ID
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Returns the sobject type.
     *
     * @return string
     */
    public function getSobjectType()
    {
        if(null === $this->sObjectType)
        {
            $this->guessSobjectType();
        }
        return $this->sObjectType;
    }

    /**
     * Guesses the sobject type out of the
     * full classname.
     *
     * @return string
     */
    protected function guessSObjectType()
    {
        $classname = get_called_class();

        $pos = strrpos($classname, '\\');

        if(false === $pos)
        {
            $this->sObjectType = $classname;
        }
        else
        {
            $this->sObjectType = substr($classname, $pos + 1);
        }
    }

    /**
     * Sets the sobject type.
     *
     * @param string sObjectType
     */
    public function setSobjectType($sObjectType)
    {
        $this->sObjectType = $sObjectType;
    }

    public function serialize()
    {
        return serialize(array(
           '__parent_serialized' => parent::serialize(),
           'Id' => $this->Id,
           'sObjectType' => $this->sObjectType
        ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        parent::unserialize($data['__parent_serialized']);

        $this->Id = $data['Id'];

        $this->sObjectType= $data['sObjectType'];
    }
}