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
    \Serializable,
    Codemitte\ForceToolkit\Soap\Header;

/**
 * SoapClientInterface
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 *
 * @interface
 * @abstract
 */
interface ClientInterface extends Serializable
{
    /**
     * Returns the Connection to the soap service as an extension
     * of Zend_Soap_Client.
     *
     * @abstract
     * @return Connection
     */
    public function getConnection();

    /**
     * Returns the TargetNamespace as a valid uri string.
     *
     * @abstract
     * @return string $uri
     */
    public function getUri();

    /**
     * Returns the API version the client implementation
     * fits to.
     *
     * @abstract
     *
     * @return string
     */
    public function getAPIVersion();

    /**
     * describeLayout()
     *
     * @param string $sObjectType
     * @param array $recordTypeIds
     * @return describeLayoutResponse
     */
    public function describeLayout($sObjectType, array $recordTypeIds = null);

    /**
     * describeSobject()
     *
     * @param string $sObjectType
     *
     * @internal param array $recordTypeIds
     *
     * @return describeSObjectResponse
     */
    public function describeSobject($sObjectType);

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
    public function query($queryString);

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
     * @return \Codemitte\ForceToolkit\Soap\Mapping\createResponse $response
     */
    public function create(
        $d,
        Header\AssignmentRuleHeader $assignmentRuleHeader = null,
        Header\MruHeader $mruHeader = null,
        Header\AllowFieldTruncationHeader $allowFieldTruncationHeader = null,
        Header\DisableFeedTrackingHeader $disableFeedTrackingHeader = null,
        Header\AllOrNoneHeader $allOrNoneHeader = null,
        Header\EmailHeader $emailHeader = null
    );

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
     * @return \Codemitte\ForceToolkit\Soap\Mapping\createResponse $response
     */
    public function update(
        $d,
        Header\AssignmentRuleHeader $assignmentRuleHeader = null,
        Header\MruHeader $mruHeader = null,
        Header\AllowFieldTruncationHeader $allowFieldTruncationHeader = null,
        Header\DisableFeedTrackingHeader $disableFeedTrackingHeader = null,
        Header\AllOrNoneHeader $allOrNoneHeader = null,
        Header\EmailHeader $emailHeader = null
    );

    /**
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Type\ID|string|array $ids
     * @throws DMLException
     * @return GenericResult
     */
    public function delete($ids);
}
