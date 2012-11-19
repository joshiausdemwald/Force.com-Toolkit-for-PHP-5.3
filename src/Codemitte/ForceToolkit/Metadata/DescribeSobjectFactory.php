<?php
namespace Codemitte\ForceToolkit\Metadata;

use
    Codemitte\ForceToolkit\Soap\Client\ClientInterface,
    Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult;

class DescribeSobjectFactory implements DescribeSobjectFactoryInterface
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
     * @internal param array $recordTypeIds
     * @return \Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult
     */
    public function getDescribe($sobjectType)
    {
        $res = $this->client->describeSobject($sobjectType);

        if($res->contains('result') && $res->get('result') instanceof DescribeSobjectResult)
        {
            return $res->get('result');
        }
        return null;
    }
}
