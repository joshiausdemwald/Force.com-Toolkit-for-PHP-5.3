<?php
namespace Codemitte\ForceToolkit\Metadata;

use Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult,
    Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutResult;

interface DescribeFormFactoryInterface
{
    /**
     * Aggregates a describe sobject result and describe layout result into
     * a more valuable representation as an underlying datastructures utilized
     * to build a form.
     *
     * If RecordTypeId is given, the matching RT setting is taken, otherwise
     * the default record type settings.
     *
     * PicklistForRecordType: Represents a single record type picklist in a RecordTypeMapping. The picklistName matches
     * up with the name attribute of each field in the fields array in describeSObjectResult. The picklistValues are the
     * set of acceptable values for the recordType.
     *
     * Note: If you retrieve picklistValues, the validFor value is null. If you need the validFor value, get it from the
     * PicklistEntry object obtained from the Field object associated with the DescribeSObjectResult.
     *
     * @abstract
     * @param $sobjectType
     * @param string|\Codemitte\ForceToolkit\Soap\Mapping\Type\ID|null $recordTypeId
     * @return DescribeFormResult
     */
    public function getDescribe($sobjectType, $recordTypeId = null);
}
