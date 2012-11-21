<?php
namespace Codemitte\ForceToolkit\Test;

use
    Codemitte\ForceToolkit\Soap\Client\PartnerClient,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Mapping\Base\login
;

class PartnerClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    private $connection;

    public function setUp()
    {
        $this->setUpConnection();
        $this->setUpClient();
    }

    private function setUpConnection()
    {
        $credentials = new login('test@test.int', 'test12345');

        $wsdl = __DIR__ . '/../../fixtures.partner.wsdl.xml';

        $serviceLocation = null;

        $this->connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);
    }



    private function setUpClient()
    {
        $this->client = new PartnerClient($this->connection);
    }
}
