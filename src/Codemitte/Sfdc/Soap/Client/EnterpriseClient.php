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
use Codemitte\Sfdc\Soap\Header;

/**
 * EnterpriseClient
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
class EnterpriseClient extends API
{
    const
        TNS = 'urn:enterprise.soap.sforce.com',
        FNS = 'urn:fault.enterprise.soap.sforce.com',
        ENS = 'urn:sobject.enterprise.soap.sforce.com';

    /**
     * Returns the TargetNamespace as a valid uri string.
     *
     * @return string $uri
     */
    public function getUri()
    {
        return self::TNS;
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
        $connection->registerClass('sObject', 'Codemitte\\Sfdc\\Soap\\Mapping\\Enterprise\\Sobject');
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
        $data = is_array($d) ? $d : array($d);

        $params = array();

        foreach($data AS $sobject)
        {
            if($data instanceof SoapVar)
            {
                $soapVar = $data;
            }
            else
            {
                if( ! $sobject instanceof SobjectInterface)
                {
                    throw new \InvalidArgumentException('$data must be an instance or a list of sObject(s).');
                }

                $param = new \stdClass();

                $param->Id =  $sobject->getId();

                foreach($sobject AS $k => $v)
                {
                    if(null !== $v && '' !== $v)
                    {
                        $param->$k = $v;
                    }
                }

                // CONVERT TO "GENERIC" SOAP VAR
                $soapVar = new SoapVar(
                    $param,
                    SOAP_ENC_OBJECT,
                    $sobject->getSobjectType(),
                    $this->getUri()
                );
            }

            // FIX FIELDS TO NULL
            $this->fixNullableFieldsVar($soapVar, $sobject->getFieldsToNull());

            $params[] = $soapVar;
        }

        return parent::create($params, $assignmentRuleHeader, $mruHeader, $allowFieldTruncationHeader, $disableFeedTrackingHeader, $allOrNoneHeader, $emailHeader);
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
    )
    {
        $data = is_array($d) ? $d : array($d);

        $params = array();

        foreach($data AS $sobject)
        {
            if($data instanceof SoapVar)
            {
                $soapVar = $data;
            }
            else
            {
                if( ! $sobject instanceof SobjectInterface)
                {
                    throw new \InvalidArgumentException('$data must be an instance or a list of sObject(s).');
                }

                $param = new \stdClass();

                $param->Id =  $sobject->getId();

                foreach($sobject AS $k => $v)
                {
                    if(null !== $v && '' !== $v)
                    {
                        $param->$k = $v;
                    }
                }

                // CONVERT TO "GENERIC" SOAP VAR
                $soapVar = new SoapVar(
                    $param,
                    SOAP_ENC_OBJECT,
                    $sobject->getSobjectType(),
                    $this->getUri()
                );
            }


            // FIX FIELDS TO NULL
            $this->fixNullableFieldsVar($soapVar, $sobject->getFieldsToNull());

            $params[] = $soapVar;
        }
        return parent::update($params, $assignmentRuleHeader, $mruHeader, $allowFieldTruncationHeader, $disableFeedTrackingHeader, $allOrNoneHeader, $emailHeader);
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
    protected function fixNullableFieldsVar(SoapVar $object, array $nullableFields = null)
    {
        if(null !== $nullableFields && count($nullableFields) > 0)
        {
            $var = new SoapVar(
                new SoapVar('<fieldsToNull>' . implode('</fieldsToNull><fieldsToNull>', $nullableFields) . '</fieldsToNull>', XSD_ANYXML), SOAP_ENC_ARRAY
            );

            if(is_array($object->enc_value))
            {
                $object->enc_value['fieldsToNull'] = $var;
            }
            else
            {
                $object->enc_value->fieldsToNull = $var;
            }
        }
    }
}

