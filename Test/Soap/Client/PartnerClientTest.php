<?php
namespace Codemitte\ForceToolkit\Test;

use
    Codemitte\ForceToolkit\Soap\Client\PartnerClient,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Mapping\Base\login
;

class PartnerClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PartnerClient
     */
    private $client;

    /**
     * @var SfdcConnection
     */
    private $connection;

    public function setUp()
    {
        $this->setUpConnection();
        $this->setUpClient();
    }

    private function setUpConnection()
    {
        $credentials = new login('johannes.heinen@gmail.com', 'luckystrike14RC2JA66VKAySU0NEXdXNTXtS');

        $wsdl = __DIR__ . '/../../fixtures/partner.wsdl.xml';

        $serviceLocation = null;

        $this->connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);
    }

    private function setUpClient()
    {
        $this->connection->login();

        $this->client = new PartnerClient($this->connection);
    }

    public function testConnection()
    {
        $this->assertTrue($this->connection->isLoggedIn());
        $this->assertTrue($this->connection->getDebug());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult', $this->connection->getLoginResult());
    }

    public function testLogout()
    {
        $this->connection->logout();

        $this->assertFalse($this->connection->isLoggedIn());

        $this->connection->login();

        $this->client = new PartnerClient($this->connection);
    }
}
