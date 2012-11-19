<?php
namespace Codemitte\ForceToolkit\Metadata;

use
    Codemitte\ForceToolkit\Soap\Client\ClientInterface,
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
     * @param \Codemitte\ForceToolkit\Soap\Client\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
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
