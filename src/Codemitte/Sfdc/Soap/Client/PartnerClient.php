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

namespace Codemitte\Sfdc\Soap\Client;

use Codemitte\Sfdc\Soap\Client\Connection\SfdcConnectionInterface;

use Codemitte\Soap\Mapping\GenericResult;
use Codemitte\Sfdc\Soap\Mapping\Partner\Sobject;
use Codemitte\Sfdc\Soap\Header;

use \SoapVar;


/**
 * PartnerClient
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
class PartnerClient extends API
{
    /**
     * Returns the TargetNamespace as a valid uri string.
     *
     * @return string $uri
     */
    public function getUri()
    {
        return 'urn:partner.soap.sforce.com';
    }

    /**
     * Different behaviour of sobject mapping in enterprise and
     * partner.wsdl requires this.
     *
     * @param Connection\SfdcConnectionInterface $connection
     */
    protected function configure(SfdcConnectionInterface $connection)
    {
        parent::configure($connection);

        // GENERIC SOBJECT
        // $connection->registerClass('sObject', 'Codemitte\\Sfdc\\Soap\\Mapping\\Partner\\Sobject');
    }

    /**
     * First key is "result"; in any way. Second is "records".
     * Always holds a GenericResultCollection.
     *
     * @param string $queryString
     * @param array $params
     * @return \Codemitte\Soap\Mapping\GenericResult|mixed
     */
    public function query($queryString, array $params = array())
    {
        /* @var $queryResponse \Codemitte\Soap\Mapping\GenericResult */
        $queryResponse = parent::query($queryString, $params);

        /* @var $queryResult \Codemitte\Soap\Mapping\GenericResult */
        $queryResult = $queryResponse->result;

        $records = null;

        if($queryResult->contains('records'))
        {
            $decoratedRecords = array();

            foreach ($queryResult->get('records') as $record)
            {
                $decoratedRecords[] = $this->toSobject($record);
            }
            $queryResult->put('records', $decoratedRecords);
        }
        return $queryResponse;
    }

    /**
     * @override
     */
    public function create(
        $d,
        Header\AssignmentRuleHeader $assignmentRuleHeader = null,
        Header\MruHeader $mruHeader = null,
        Header\AllowFieldTruncationHeader $allowFieldTruncationHeader = null,
        Header\DisableFeedTrackingHeader $disableFeedTrackingHeader = null,
        Header\AllOrNoneHeader $allOrNoneHeader = null,
        Header\EmailHeader $emailHeader = null
    )
    {
        $sobjects = is_array($d) ? $d : array($d);

        $data = array();

        foreach($sobjects AS $sobject)
        {
            $data[] = $this->fromSobject($sobject);
        }

        return parent::create($data, $assignmentRuleHeader, $mruHeader, $allowFieldTruncationHeader, $disableFeedTrackingHeader, $allOrNoneHeader, $emailHeader);
    }

    /**
     * @override
     */
    public function update(
        $d,
        Header\AssignmentRuleHeader $assignmentRuleHeader = null,
        Header\MruHeader $mruHeader = null,
        Header\AllowFieldTruncationHeader $allowFieldTruncationHeader = null,
        Header\DisableFeedTrackingHeader $disableFeedTrackingHeader = null,
        Header\AllOrNoneHeader $allOrNoneHeader = null,
        Header\EmailHeader $emailHeader = null
    ) {
        $sobjects = is_array($d) ? $d : array($d);

        $data = array();

        foreach($sobjects AS $sobject)
        {
            $data[] = $this->fromSobject($sobject);
        }
        return parent::update($data, $assignmentRuleHeader, $mruHeader, $allowFieldTruncationHeader, $disableFeedTrackingHeader, $allOrNoneHeader, $emailHeader);
    }


    /**
     * @param \Codemitte\Sfdc\Soap\Mapping\Partner\Sobject $sobject
     * @return \stdClass
     */
    protected function fromSobject(Sobject $sobject)
    {
        $retVal = new \stdClass();

        if(null !== $sobject->getId())
        {
            $retVal->Id = (string)$sobject->getId();
        }

        $any = $this->toAny($sobject->toArray());

        $retVal->type = $sobject->getSobjectType();

        if(strlen($any) > 0)
        {
            $retVal->any = $any;
        }

        // FIX FIELDS TO NULL
        $this->fixNullableFieldsVar($retVal,  $sobject->getFieldsToNull());

        return $retVal;
    }

    /**
     * @param $any
     * @return string
     */
    public function toAny(array $fields)
    {
        $any = '';

        foreach($fields AS $key => $value)
        {
            if($value !== null && $value !== '')
            {
                $any .= <<<EOF
<$key>$value</$key>
EOF;
            }
        }
        return $any;
    }

    /**
     * Cleans up an sobject, removes duplicate ids, performs
     * transformation of <any>-fields.
     *
     * @param $record
     * @return \Codemitte\Sfdc\Soap\Mapping\Partner\Sobject
     */
    public function toSobject($record)
    {
        $data = array();

        $type = $record['type'];

        // CLEANUP ID, CONVERT LIST OF INTO SINGLE ID
        $id = null;

        if(isset($record['Id']))
        {
            $id = $record['Id'];

            if(is_array($id) || $id instanceof \Traversable)
            {
                $id = $id[0];
            }
        }

        $data['Id'] = $id;

        if(isset($record['any']))
        {
            $data = array_merge($data, $this->cleanupAnyField($record['any']));
        }

        return new Sobject($type, $data);
    }

    /**
     * Cleans up the "rest" of sub-nested data beneath the any-"array" which
     * actually is a list AND a map. -.- <3 it.
     *
     * @see toSobject()
     *
     * @param $any
     * @return array
     */
    protected function cleanupAnyField($any)
    {
        $retVal = array();

        if(is_string($any))
        {
            $xmlStr = $any;
        }
        else
        {
            $xmlStr = $any[0];

            unset($any[0]);

            foreach($any AS $key => $value)
            {
                $retVal[$key] = $this->toSobject($value);
            }
        }
        return array_merge($this->fromAny($xmlStr), $retVal);
    }

    /**
     * Converts a raw "<any..." xml stream into a php object
     * representation.
     *
     * @param $anyXml
     * @return array
     */
    public function fromAny($anyXml, $prefix = 'sf', $tns = null)
    {
        if( ! is_string($anyXml))
        {
            return $anyXml;
        }

        $retVal = array();

        null === $tns && $tns = $this->getUri();

        $anyXml = <<<EOF
<?xml version="1.0" standalone="yes"?>
<data
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:$prefix="$tns">$anyXml</data>
EOF;
        /* @var $xml \SimpleXMLElement */
        $xml = new \SimpleXMLElement($anyXml);

        // CASTING MAGICK
        foreach((array)$xml->children($prefix, true) AS $key => $value)
        {
            if($value instanceof \SimpleXMLElement)
            {
                $atts = $value->attributes('xsi', true);

                if(true === (bool)$atts['nil'])
                {
                    $value = null;
                }
                else
                {
                    $value = '';
                }
            }
            else
            {
                $retVal[$key] = $value;
            }
        }
        return $retVal;
    }

    /**
     * getNullableFieldsVar()
     * Used in CRU(D)-Methods to fix and add the fieldsToNull property in a Salesforce-way.
     *
     * @param SoapVar $object
     * @param array $nullableFields
     *
     * @return void
     */
    protected function fixNullableFieldsVar(\stdClass $object, array $nullableFields = null)
    {
        if(null !== $nullableFields && count($nullableFields) > 0)
        {
            $var = new SoapVar(
                new SoapVar('<fieldsToNull>' . implode('</fieldsToNull><fieldsToNull>', $nullableFields) . '</fieldsToNull>', XSD_ANYXML), SOAP_ENC_ARRAY
            );
            $object->fieldsToNull = $var;
        }
    }
}