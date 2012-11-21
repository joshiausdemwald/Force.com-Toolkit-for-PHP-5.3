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
    private static $client;

    /**
     * @var SfdcConnection
     */
    private static $connection;

    public static function setUpBeforeClass()
    {
        self::setUpConnection();
        self::setUpClient();
    }

    private static function setUpConnection()
    {
        $credentials = new login(SFDC_USERNAME, SFDC_PASSWORD);

        $wsdl = __DIR__ . '/../../fixtures/partner.wsdl.xml';

        $serviceLocation = null;

        self::$connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);
    }

    private static function setUpClient()
    {
        self::$connection->login();

        self::$client = new PartnerClient(self::$connection);
    }

    public function testConnection()
    {
        $this->assertTrue(self::$connection->isLoggedIn());
        $this->assertTrue(self::$connection->getDebug());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult', self::$connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', self::$connection->getCredentials());
    }

    public function testConnectionInputHeaders()
    {
        $headers = self::$connection->getSoapInputHeaders();

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\SessionHeader', $headers[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\CallOptions', $headers[1]);

        $this->assertNotEmpty($headers[0]->getSessionId());
    }

    public function testLogout()
    {
        self::$connection->logout();

        $this->assertFalse(self::$connection->isLoggedIn());
        $this->assertNull(self::$connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', self::$connection->getCredentials());

        self::$connection->login();

        self::$client = new PartnerClient(self::$connection);
    }

    public function testClient()
    {
        $this->assertEquals('urn:partner.soap.sforce.com', self::$client->getUri());
        $this->assertEquals('26.0', self::$client->getAPIVersion()); // hard coded, should match wsdl. @todo: refactor me.
    }

    public function testClientToAny()
    {
        $obj = array(
            'testkey'=> 'testvalue',
            'testkey2__c' => 'testvalue2'
        );

        $target = new \stdClass();

        self::$client->toAny($obj, $target);

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

        $result = self::$client->fromSobject($sobject);

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

        $response = self::$client->create($sobject);

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty(true, $response->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $response->get('result'));
        $this->assertNotCount(0, $response->get('result'));
        $this->assertEquals(1, $response->get('result')->get(0)->get('success'));
        $this->assertNotEmpty($response->get('result')->get(0)->get('id'));

        $response = self::$client->delete($response->get('result')->get(0)->get('id'));

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty(true, $response->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $response->get('result'));
        $this->assertNotCount(0, $response->get('result'));
        $this->assertEquals(1, $response->get('result')->get(0)->get('success'));
    }
}

