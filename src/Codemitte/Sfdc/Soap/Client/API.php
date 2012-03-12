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

use \InvalidArgumentException;

use Codemitte\Sfdc\Soap\Client\Connection\SfdcConnectionInterface;
use Codemitte\Sfdc\Soql\Parser\QueryParserInterface;
use Codemitte\Sfdc\Soql\Parser\QueryParser;

/**
 * API. Abstract parent class for partner
 * and Enterprise wsdl.
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 *
 * @abstract
 */
abstract class API extends BaseClient
{
    /**
     * @var QueryParser|null
     */
    protected $queryParser;

    /**
     * Constructor.
     *
     * @param Connection\SfdcConnectionInterface $connection
     * @param QueryParser|null $queryParser
     */
    public function __construct(SfdcConnectionInterface $connection, QueryParserInterface $queryParser = null)
    {
        parent::__construct($connection);

        if(null === $queryParser)
        {
            $queryParser = new QueryParser();
        }

        $this->queryParser = $queryParser;

        $connection->registerClass('DescribeLayout', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayout');
        $connection->registerClass('DescribeLayoutButton', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutButton');
        $connection->registerClass('DescribeLayoutButtonSection', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutButtonSection');
        $connection->registerClass('DescribeLayoutComponent', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutComponent');
        $connection->registerClass('DescribeLayoutItem', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutItem');
        $connection->registerClass('describeLayoutResponse', 'Codemitte\\Sfdc\\Soap\\Mapping\\describeLayoutResponse');
        $connection->registerClass('DescribeLayoutResult', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutResult');
        $connection->registerClass('DescribeLayoutRow', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutRow');
        $connection->registerClass('DescribeLayoutSection', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutSection');
        $connection->registerClass('QueryResult', 'Codemitte\\Sfdc\\Soap\\Mapping\\QueryResult');
        $connection->registerClass('createResponse', 'Codemitte\\Sfdc\\Soap\\Mapping\\createResponse');

        $connection->registerType('ID', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\ID', $this->getUri());
        $connection->registerType('QueryLocator', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\QueryLocator', $this->getUri());
        $connection->registerType('StatusCode', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\StatusCode', $this->getUri());
        $connection->registerType('fieldType', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\fieldType', $this->getUri());
        $connection->registerType('soapType', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\soapType', $this->getUri());
        $connection->registerType('layoutComponentType', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\layoutComponentType', $this->getUri());
        $connection->registerType('EmailPriority', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\EmailPriority', $this->getUri());
        $connection->registerType('DebugLevel', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\DebugLevel', $this->getUri());
        $connection->registerType('ExceptionCode', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\ExceptionCode', $this->getUri());
    }

    /**
     * describeLayout()
     *
     * @param string $sObjectType
     * @param array $recordTypeIds
     * @return \Codemitte\Sfdc\Soap\Mapping\describeLayoutResponse
     */
    public function describeLayout($sObjectType, array $recordTypeIds = null)
    {
        return $this->getConnection()->soapCall(
            'describeLayout',
            array(
                 array(
                     'sObjectType' => $sObjectType,
                     'recordTypeIds' => $recordTypeIds
                 )
            )
        );
    }

    /**
     * describeSobject()
     *
     * @param string $sObjectType
     *
     * @internal param array $recordTypeIds
     *
     * @return describeSObjectResponse
     */
    public function describeSobject($sObjectType)
    {
        return $this->getConnection()->soapCall(
            'describeSobject',
            array(
                 array(
                     'sObjectType' => $sObjectType
                 )
            )
        );
    }

    /**
     * query()
     *
     * Performs an arbitrary soql query against
     * the database.
     *
     * <soap:header use="literal" message="tns:Header" part="SessionHeader"/>
     * <soap:header use="literal" message="tns:Header" part="CallOptions"/>
     * <soap:header use="literal" message="tns:Header" part="QueryOptions"/>
     * <soap:header use="literal" message="tns:Header" part="MruHeader"/>
     * <soap:header use="literal" message="tns:Header" part="PackageVersionHeader"/>
     * <soap:body parts="parameters" use="literal"/>
     *
     * @param string $queryString
     * @return mixed $result
     */
    public function query($queryString, array $params = array())
    {
        return $this->getConnection()->soapCall(
            'query', array(array(
                    'queryString' => $this->queryParser->parse($queryString, $params)
            ))
        );
    }

    /**
     * create() persists new rows to the database.
     *
     * <soap:header use="literal" message="tns:Header" part="SessionHeader"/>
     * <soap:header use="literal" message="tns:Header" part="AssignmentRuleHeader"/>
     * <soap:header use="literal" message="tns:Header" part="MruHeader"/>
     * <soap:header use="literal" message="tns:Header" part="AllowFieldTruncationHeader"/>
     * <soap:header use="literal" message="tns:Header" part="DisableFeedTrackingHeader"/>
     * <soap:header use="literal" message="tns:Header" part="StreamingEnabledHeader"/>
     * <soap:header use="literal" message="tns:Header" part="AllOrNoneHeader"/>
     * <soap:header use="literal" message="tns:Header" part="DebuggingHeader"/>
     * <soap:header use="literal" message="tns:Header" part="PackageVersionHeader"/>
     * <soap:header use="literal" message="tns:Header" part="EmailHeader"/>
     *
     * @param \Codemitte\Sfdc\Soap\Mapping\Sobject|array|\Traversable $data: List of sobjects
     * @return \Codemitte\Sfdc\Soap\Mapping\createResponse $response
     */
    public function create($d)
    {
        $data = is_array($d) ? $d : array($d);

        $params = array();

        foreach($data AS $key => $sobject)
        {
            if( ! $sobject instanceof \Codemitte\Sfdc\Soap\Mapping\sObject)
            {
                throw new InvalidArgumentException('$data must be an instance or a list of sObject(s).');
            }

            $param = new \stdClass();

            $param->Id =  $sobject->getId();

            foreach($sobject AS $k => $v)
            {
                if(null !== $v)
                {
                    $param->$k = $v;
                }
            }

            // CONVERT TO "GENERIC" SOAP VAR
            $soapVar = new \SoapVar(
                $param,
                SOAP_ENC_OBJECT,
                $sobject->getSobjectType(),
                $this->getUri()
            );

            // FIX FIELDS TO NULL
            $this->fixNullableFieldsVar($soapVar, $sobject->getFieldsToNull());

            $params[$key] = $soapVar;
        }

        return $this->getConnection()->soapCall('create', array(array(
            'sObjects' => $params
        )));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        return serialize(array('queryParser' => $this->queryParser, '__parentData' => parent::serialize()));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     *
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        parent::unserialize($data['__parentData']);

        $this->queryParser= $data['queryParser'];
    }

    /**
     * getNullableFieldsVar()
     * Used in CRU(D)-Methods to fix and add the fieldsToNull property in a Salesforce-way.
     *
     * @param \SoapVar $object
     * @param array $nullableFields
     *
     * @return void
     */
    protected function fixNullableFieldsVar(\SoapVar $object, array $nullableFields)
    {
        if(count($nullableFields) > 0)
        {
            $var = new \SoapVar(
                new \SoapVar('<fieldsToNull>' . implode('</fieldsToNull><fieldsToNull>', $nullableFields) . '</fieldsToNull>', XSD_ANYXML), SOAP_ENC_ARRAY
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
