<?php
namespace Codemitte\ForceToolkit\Test;

use
    Codemitte\ForceToolkit\Soap\Client\EnterpriseClient,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Mapping\Base\login,
    Codemitte\ForceToolkit\Soap\Mapping\Sobject
;

class EnterpriseClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnterpriseClient
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

        $wsdl = __DIR__ . '/../../fixtures/enterprise.wsdl.xml';

        $serviceLocation = SFDC_SERVICE_LOCATION ? SFDC_SERVICE_LOCATION : null;

        self::$connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);
    }

    private static function setUpClient()
    {
        self::$connection->login();

        self::$client = new EnterpriseClient(self::$connection);
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

        self::$client = new EnterpriseClient(self::$connection);
    }

    public function testClient()
    {
        $this->assertEquals('urn:enterprise.soap.sforce.com', self::$client->getUri());
        $this->assertEquals('26.0', self::$client->getAPIVersion()); // hard coded, should match wsdl. @todo: refactor me.
    }

    public function testDML()
    {
        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse = self::$client->create($sobject);

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $createResponse);
        $this->assertNotEmpty(true, $createResponse->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $createResponse->get('result'));
        $this->assertNotCount(0, $createResponse->get('result'));
        $this->assertEquals(1, $createResponse->get('result')->get(0)->get('success'));
        $this->assertNotEmpty($createResponse->get('result')->get(0)->get('id'));

        // ID OF sObject empty?
        $this->assertEmpty($sobject->getId());

        $sobject = new Sobject('Contact', array(
            'Id' => $createResponse->get('result')->get(0)->get('id'),
            'Title' => 'Graf'
        ));

        $updateResponse = self::$client->update($sobject);

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $updateResponse);
        $this->assertNotEmpty(true, $updateResponse->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $updateResponse->get('result'));
        $this->assertNotCount(0, $updateResponse->get('result'));
        $this->assertEquals(1, $updateResponse->get('result')->get(0)->get('success'));

        $deleteResponse = self::$client->delete($createResponse->get('result')->get(0)->get('id'));

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $deleteResponse);
        $this->assertNotEmpty(true, $deleteResponse->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $deleteResponse->get('result'));
        $this->assertNotCount(0, $deleteResponse->get('result'));
        $this->assertEquals(1, $deleteResponse->get('result')->get(0)->get('success'));
    }

    public function testCreateSobjectNegative()
    {
        $sobject = new Sobject('Contact', array('FirstName' => 'zurbelnase'));
        $exThrown = null;
        try
        {
            self::$client->create($sobject);
        }
        catch(\Exception $e)
        {
            $exThrown = $e;
        }
        $this->assertNotNull($exThrown);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Client\DMLException', $exThrown);
    }

    public function testDescribeSobject()
    {
        $response = self::$client->describeSobject('Contact');

        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty($response->get('result'));
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult', $response->get('result'));
    }

    public function testDescribeLayout()
    {
        $response = self::$client->describeLayout('Contact');

        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty($response->get('result'));
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutResult', $response->get('result'));
    }

    public function testQuery()
    {
        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse= self::$client->create($sobject);

        $queryResponse = self::$client->query('SELECT Id, Salutation, Title, FirstName, LastName FROM Contact WHERE Id = \'' . $createResponse->get('result')->get(0)->get('id') . '\'');

        $this->assertNotEmpty($queryResponse);
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $queryResponse);
        $this->assertNotEmpty($queryResponse->get('result'));
        // $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\QueryResult', $queryResponse->get('result'));
        $this->assertNotCount(0, $queryResponse->get('result')->getRecords());
        $this->assertEquals(1, $queryResponse->get('result')->getSize());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface', $queryResponse->get('result')->getRecords()->get(0));
        $this->assertEquals('Mr', $queryResponse->get('result')->getRecords()->get(0)->get('Salutation'));
        $this->assertEquals(null, $queryResponse->get('result')->getRecords()->get(0)->get('Title'));
        $this->assertEquals('Hans', $queryResponse->get('result')->getRecords()->get(0)->get('FirstName'));
        $this->assertEquals('Wurst', $queryResponse->get('result')->getRecords()->get(0)->get('LastName'));

        self::$client->delete($createResponse->get('result')->get(0)->get('id'));
    }

    public function testQueryNegative()
    {
        $exThrown = null;

        try
        {
            self::$client->query('FROM Dingsda SELECT Nix');
        }
        catch(\Exception $e)
        {
            $exThrown = $e;
        }

        $this->assertNotNull($exThrown);
        $this->assertInstanceOf('\Codemitte\Soap\Client\Connection\TracedSoapFault', $exThrown);
        $this->assertEquals('MALFORMED_QUERY: unexpected token: FROM', $exThrown->getMessage());
    }

    public function testQueryAll()
    {
        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse= self::$client->create($sobject);

        self::$client->delete($createResponse->get('result')->get(0)->get('id'));

        $queryResponse = self::$client->queryAll('SELECT Id, Salutation, Title, FirstName, LastName FROM Contact WHERE Id = \'' . $createResponse->get('result')->get(0)->get('id') . '\'');

        $this->assertNotEmpty($queryResponse);
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $queryResponse);
        $this->assertNotEmpty($queryResponse->get('result'));
        // $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\QueryResult', $queryResponse->get('result'));
        $this->assertNotCount(0, $queryResponse->get('result')->getRecords());
        $this->assertEquals(1, $queryResponse->get('result')->getSize());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface', $queryResponse->get('result')->getRecords()->get(0));
        $this->assertEquals('Mr', $queryResponse->get('result')->getRecords()->get(0)->get('Salutation'));
        $this->assertEquals(null, $queryResponse->get('result')->getRecords()->get(0)->get('Title'));
        $this->assertEquals('Hans', $queryResponse->get('result')->getRecords()->get(0)->get('FirstName'));
        $this->assertEquals('Wurst', $queryResponse->get('result')->getRecords()->get(0)->get('LastName'));


    }
}

