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

namespace Codemitte\ForceToolkit\Soap\Client;

use \SoapVar;

use Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface;
use Codemitte\ForceToolkit\Soap\Mapping\Sobject;
use Codemitte\ForceToolkit\Soap\Header;
use Codemitte\ForceToolkit\Soap\Mapping\Type\ID;

/**
 * PartnerClient
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
final class PartnerClient extends API
{
    const
        FNS='urn:fault.partner.soap.sforce.com',
        TNS='urn:partner.soap.sforce.com',
        ENS='urn:sobject.partner.soap.sforce.com'
    ;

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
     * @param \Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface $sobject
     * @return \stdClass
     */
    public function fromSobject(SobjectInterface $sobject)
    {
        $retVal = new \stdClass();

        if(null !== $sobject->getId())
        {
            $retVal->Id = (string)$sobject->getId();
        }

        $this->toAny($sobject->toArray(), $retVal);


        $retVal->type = $sobject->getSobjectType();

        // FIX FIELDS TO NULL
        $this->fixNullableFieldsVar($retVal,  $sobject->getFieldsToNull());

        return $retVal;
    }

    /**
     * @param array $fields
     * @param \stdClass $target
     * @throws \RuntimeException
     * @return void
     */
    public function toAny(array $fields, \stdClass $target)
    {
        $anyStr = '';

        foreach($fields AS $key => $value)
        {
            if($value !== null && $value !== '')
            {
                if(is_scalar($value))
                {
                    $v = $value;
                    $anyStr .= <<<EOF
<$key><![CDATA[$v]]></$key>
EOF;
                }

                elseif($value instanceof Sobject)
                {
                    $target->$key = $this->fromSobject($value);
                }

                // REGARD RENEWED ID HANDLING ...
                elseif($value instanceof ID)
                {
                    $target->Id = (string)$value;
                }

                // RELATED LIST?
                else
                {
                    throw new \RuntimeException(sprintf('Type "%s" is (currently) not supported in DML calls.', gettype($value)));
                }
            }
        }

        if(is_array($anyStr) || strlen($anyStr) > 0)
        {
            $target->any = $anyStr;
        }
    }

    /**
     * fixNullableFieldsVar()
     * Used in CRU(D)-Methods to fix and add the fieldsToNull property in a Salesforce-way.
     *
     * @param \SoapVar|\stdClass $object
     * @param array $nullableFields
     *
     * @return void
     */
    private function fixNullableFieldsVar(\stdClass $object, array $nullableFields = null)
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