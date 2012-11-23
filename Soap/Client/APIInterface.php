<?php
namespace Codemitte\ForceToolkit\Soap\Client;

use
    Codemitte\ForceToolkit\Soap\Header,
    Codemitte\ForceToolkit\Soap\Mapping\Type\QueryLocator
;

/**
 * APIInterface
 *
 * Interface for defining salesforce SOAP API clients
 * (based on partner/enterprise wsdl)
 */
interface APIInterface extends BaseClientInterface
{
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
     * @param int|null $batchSize
     * @return mixed $result
     */
    public function query($queryString, $batchSize = null);

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
    public function queryAll($queryString, $batchSize = null);

    /**
     * queryMore() Retrieves the next batch of objects from a query().
     *     QueryResult = connection.queryMore( QueryLocator QueryLocator);
     * Use this call to process query() calls that retrieve a large number of
     * records (by default, more than 500) in the result set.
     *
     * <soap:header use="literal" message="tns:Header" part="SessionHeader"/>
     * <soap:header use="literal" message="tns:Header" part="CallOptions"/>
     * <soap:header use="literal" message="tns:Header" part="QueryOptions"/>
     * <soap:header use="literal" message="tns:Header" part="MruHeader"/>
     * <soap:header use="literal" message="tns:Header" part="PackageVersionHeader"/>
     *
     * @param QueryLocator $queryLocator
     * @return mixed $result
     */
    public function queryMore(QueryLocator $queryLocator);

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
     * @return \Codemitte\Soap\Mapping\GenericResult $response
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
     * @return \Codemitte\Soap\Mapping\GenericResult $response
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

    /**
     * Use this call to end one or more sessions.
     * You can also use logout() to end just one session, the session of the logged-in user.
     *
     * @param $sessionIds
     * @return InvalidateSessionsResult = connection.invalidateSessions(string[] sessionIds);
     */
    public function invalidateSessions($sessionIds);
}
