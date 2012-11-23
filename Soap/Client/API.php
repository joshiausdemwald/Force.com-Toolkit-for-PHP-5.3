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

use
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnectionInterface,
    Codemitte\ForceToolkit\Soap\Header,
    Codemitte\ForceToolkit\Soap\Client\DMLException,
    Codemitte\ForceToolkit\Soap\Mapping\Type\QueryLocator
;

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
abstract class API extends BaseClient implements APIInterface
{
    /**
     * Constructor.
     *
     * @param Connection\SfdcConnectionInterface $connection
     */
    public function __construct(SfdcConnectionInterface $connection)
    {
        parent::__construct($connection);

        // OPTIONAL. SEEN THAT IN SOME REFERENCE IMPL.
        $connection->setOption('actor', $this->getUri());

        $connection->registerClass('DescribeLayout', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayout');
        $connection->registerClass('DescribeLayoutButton', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutButton');
        $connection->registerClass('DescribeLayoutButtonSection', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutButtonSection');
        $connection->registerClass('DescribeLayoutComponent', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutComponent');
        $connection->registerClass('DescribeLayoutItem', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutItem');
        $connection->registerClass('DescribeLayoutResult', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutResult');
        $connection->registerClass('DescribeLayoutRow', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutRow');
        $connection->registerClass('DescribeLayoutSection', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeLayoutSection');
        $connection->registerClass('RecordTypeMapping', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\RecordTypeMapping');
        $connection->registerClass('PicklistForRecordType', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\PicklistForRecordType');

        // $connection->registerClass('QueryResult', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\QueryResult');
        $connection->registerClass('sObject', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Sobject');
        $connection->registerClass('DescribeSObjectResult', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\DescribeSObjectResult');
        $connection->registerClass('RecordTypeInfo', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\RecordTypeInfo');
        $connection->registerClass('RecordType', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\RecordType');
        $connection->registerClass('ChildRelationship', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\ChildRelationship');
        $connection->registerClass('Field', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Field');
        $connection->registerClass('PicklistEntry', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\PicklistEntry');

        $connection->registerType('ID', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\ID', $this->getUri());
        $connection->registerType('QueryLocator', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\QueryLocator', $this->getUri());
        $connection->registerType('StatusCode', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\StatusCode', $this->getUri());
        $connection->registerType('fieldType', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\fieldType', $this->getUri());
        $connection->registerType('soapType', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\soapType', $this->getUri());
        $connection->registerType('layoutComponentType', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\layoutComponentType', $this->getUri());
        $connection->registerType('EmailPriority', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\EmailPriority', $this->getUri());
        $connection->registerType('DebugLevel', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\DebugLevel', $this->getUri());
        $connection->registerType('ExceptionCode', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Type\\ExceptionCode', $this->getUri());
    }

    /**
     * describeLayout()
     *
     * @param string $sObjectType
     * @param array $recordTypeIds
     * @return describeLayoutResponse
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
     * @param int|null $batchSize
     * @return mixed $result
     */
    public function query($queryString, $batchSize = null)
    {
        if(null !== $batchSize)
        {
            $this->getConnection()->addSoapInputHeader(new Header\QueryOptions($this->getConnection()->getURI(), $batchSize));
        }
        return $this->getConnection()->soapCall(
            'query',
            array(array(
                'queryString' => $queryString
            ))
        );
    }

    /**
     * queryAll()
     *
     * Performs an arbitrary soql query against
     * the database, delivering deleted records, too.
     *
     * <soap:header use="literal" message="tns:Header" part="SessionHeader"/>
     * <soap:header use="literal" message="tns:Header" part="CallOptions"/>
     * <soap:header use="literal" message="tns:Header" part="QueryOptions"/>
     * <soap:header use="literal" message="tns:Header" part="MruHeader"/>
     * <soap:header use="literal" message="tns:Header" part="PackageVersionHeader"/>
     * <soap:body parts="parameters" use="literal"/>
     *
     * @param string $queryString
     * @param int|null $batchSize
     * @return mixed $result
     */
    public function queryAll($queryString, $batchSize = null)
    {
        if(null !== $batchSize)
        {
            $this->getConnection()->addSoapInputHeader(new Header\QueryOptions($this->getConnection()->getURI(), $batchSize));
        }
        return $this->getConnection()->soapCall(
            'queryAll',
            array(array(
                'queryString' => $queryString
            ))
        );

        return $res;
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
     * @param object|array $d
     * @param \Codemitte\ForceToolkit\Soap\Header\AssignmentRuleHeader|null $assignmentRuleHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\MruHeader|null $mruHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\AllowFieldTruncationHeader|null $allowFieldTruncationHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\DisableFeedTrackingHeader|null $disableFeedTrackingHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\AllOrNoneHeader|null $allOrNoneHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\EmailHeader|null $emailHeader
     *
     * @throws DMLException
     * @return createResponse $response
     */
    public function create(
        $d,
        Header\AssignmentRuleHeader $assignmentRuleHeader = null,
        Header\MruHeader $mruHeader = null,
        Header\AllowFieldTruncationHeader $allowFieldTruncationHeader = null,
        Header\DisableFeedTrackingHeader $disableFeedTrackingHeader = null,
        Header\AllOrNoneHeader $allOrNoneHeader = null,
        Header\EmailHeader $emailHeader = null
    ) {
        $data = is_array($d) ? $d : array($d);

        try
        {
            $res = $this->getConnection()->soapCall('create', array(array(
                'sObjects' => $data
            )));

            return $this->throwDMLException($res);
        }
        catch(\Exception $e)
        {
            throw new DMLException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * update() updates rows in the database.
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
     * @param object|array $d
     * @param \Codemitte\ForceToolkit\Soap\Header\AssignmentRuleHeader|null $assignmentRuleHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\MruHeader|null $mruHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\AllowFieldTruncationHeader|null $allowFieldTruncationHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\DisableFeedTrackingHeader|null $disableFeedTrackingHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\AllOrNoneHeader|null $allOrNoneHeader
     * @param \Codemitte\ForceToolkit\Soap\Header\EmailHeader|null $emailHeader
     *
     * @throws DMLException
     * @return createResponse $response
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
        $data = is_array($d) ? $d : array($d);

        try
        {
            $res = $this->getConnection()->soapCall('update', array(array(
                'sObjects' => $data
            )));

            return $this->throwDMLException($res);
        }
        catch(\Exception $e)
        {
            throw new DMLException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Type\ID|string|array $ids
     * @throws DMLException
     * @return GenericResult
     */
    public function delete($ids)
    {
        if( ! is_array($ids))
        {
            $ids = array($ids);
        }

        foreach($ids AS & $id)
        {
            $id = (string) $id;
        }

        try
        {
            return $this->getConnection()->soapCall('delete', array('ids' => $ids));
        }
        catch(\Exception $e)
        {
            throw new DMLException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws DMLException
     * @param $res
     * @return $res
     */
    protected function throwDMLException($res)
    {
        if($res->contains('result') && $res->get('result')->count() > 0)
        {
            $result = $res->get('result')->get(0);

            if($result->contains('success') && true === $result->get('success'))
            {
                return $res;
            }

            if($result->contains('errors'))
            {
                $e    = null;

                foreach($result->get('errors') AS $error)
                {
                    $fields = array();

                    if($error->contains('fields'))
                    {
                        foreach($error->get('fields') AS $field)
                        {
                            $fields[] = '"' . $field . '"';
                        }
                    }
                    $fields = implode(', ', $fields);

                    $e = new DMLException('Message: "' . $error->get('message') . '", Code: "' . (string)$error->get('statusCode') . '", Fields: [' . $fields . ']', null, $e);
                }
                throw $e;
            }
        }
        throw new DMLException('No result');
    }

    /**
     * queryMore() Retrieves the next batch of objects from a query().
     *     QueryResult = connection.queryMore( QueryLocator QueryLocator);
     * Use this call to process query() calls that retrieve a large number of
     * records (by default, more than 500) in the result set.
     *
     * @param QueryLocator $queryLocator: A specialized string, similar to ID.
     *                     Used in the subsequent queryMore() call for retrieving sets
     *                     of objects from the query results, if applicable.
     * @return mixed $result
     */
    public function queryMore(QueryLocator $queryLocator)
    {
        return $this->getConnection()->soapCall('queryMore', array(
            array('queryLocator' => (string)$queryLocator)
        ));
    }
}
