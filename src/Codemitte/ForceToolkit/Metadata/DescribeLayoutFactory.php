<?php
namespace Codemitte\ForceToolkit\Metadata;

use
    Codemitte\ForceToolkit\Soap\Client\APIInterface,
    Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutResult;

class DescribeLayoutFactory implements DescribeLayoutFactoryInterface
{
    /**
     * @var ClientInterface $client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param APIInterface $client
     */
    public function __construct(APIInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $sobjectType
     * @param array $recordTypeIds
     * @return \Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult
     */
    public function getDescribe($sobjectType, array $recordTypeIds = null)
    {
        $res = $this->client->describeLayout($sobjectType, $recordTypeIds);

        if($res->contains('result') && $res->get('result') instanceof DescribeLayoutResult)
        {
            return $res->get('result');
        }
        return null;
    }
}
