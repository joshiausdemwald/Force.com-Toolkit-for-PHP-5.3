<?php
namespace Codemitte\ForceToolkit\Test;

use
    Codemitte\ForceToolkit\Soap\Client\PartnerClient,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Mapping\Base\login,
    Codemitte\ForceToolkit\Soap\Mapping\Partner\Sobject
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
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', $this->connection->getCredentials());
    }

    public function testConnectionInputHeaders()
    {
        $headers = $this->connection->getSoapInputHeaders();

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\SessionHeader', $headers[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\CallOptions', $headers[1]);

        $this->assertNotEmpty($headers[0]->getSessionId());
    }

    public function testLogout()
    {
        $this->connection->logout();

        $this->assertFalse($this->connection->isLoggedIn());
        $this->assertNull($this->connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', $this->connection->getCredentials());

        $this->connection->login();

        $this->client = new PartnerClient($this->connection);
    }

    public function testClient()
    {
        $this->assertEquals('urn:partner.soap.sforce.com', $this->client->getUri());
        $this->assertEquals('26.0', $this->client->getAPIVersion()); // hard coded, should match wsdl. @todo: refactor me.
    }

    public function testClientToAny()
    {
        $obj = array(
            'testkey'=> 'testvalue',
            'testkey2__c' => 'testvalue2'
        );

        $target = new \stdClass();

        $this->client->toAny($obj, $target);

        $this->assertNotEmpty($target->any);

        $this->assertEquals('<testkey><![CDATA[testvalue]]></testkey><testkey2__c><![CDATA[testvalue2]]></testkey2__c>', $target->any);
    }

    public function testClientFromSobject()
    {
        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $result = $this->client->fromSobject($sobject);

        $this->assertInstanceOf('\stdClass', $result);

        $this->assertEquals('Contact', $result->type);
        $this->assertEquals('<Salutation><![CDATA[Mr]]></Salutation><FirstName><![CDATA[Hans]]></FirstName><LastName><![CDATA[Wurst]]></LastName>', $result->any);
        $this->assertObjectHasAttribute('fieldsToNull', $result);
        $this->assertInstanceOf('\SoapVar', $result->fieldsToNull);

        // WEIRD... @todo: never change a running system, but consider taking a look.
        $this->assertEquals('<fieldsToNull>Title</fieldsToNull>', $result->fieldsToNull->enc_value->enc_value);
    }

    public function testCreateSobject()
    {
        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $response = $this->client->create($sobject);

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty(true, $response->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $response->get('result'));
        $this->assertNotCount(0, $response->get('result'));
        $this->assertEquals(1, $response->get('result')->get(0)->get('success'));
        $this->assertNotEmpty($response->get('result')->get(0)->get('id'));

        $response = $this->client->delete($response->get('result')->get(0)->get('id'));

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty(true, $response->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $response->get('result'));
        $this->assertNotCount(0, $response->get('result'));
        $this->assertEquals(1, $response->get('result')->get(0)->get('success'));
    }
}

