<?php
namespace Codemitte\Sfdc\Soap\Client;

use Codemitte\Sfdc\Soap\Client\Connection\SfdcConnectionInterface;

/**
 * EnterpriseClient
 */
class EnterpriseClient extends API
{
    /**
     * @param SfdcConnectionInterface $connection
     */
    protected function configure(SfdcConnectionInterface $connection)
    {

    }

    /**
     * Returns the TargetNamespace as a valid uri string.
     *
     * @return string $uri
     */
    public function getUri()
    {
        return 'urn:enterprise.soap.sforce.com';
    }
}

