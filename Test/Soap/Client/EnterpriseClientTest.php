<?php
namespace Codemitte\ForceToolkit\Test\Soap\Client;

use
    Codemitte\ForceToolkit\Soap\Client\EnterpriseClient,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Mapping\Base\login,
    Codemitte\ForceToolkit\Soap\Mapping\Sobject
;

class EnterpriseClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnectionInterface
     */
    private static function getConnection()
    {
        $credentials = new login(SFDC_USERNAME, SFDC_PASSWORD);

        $wsdl = __DIR__ . '/../../fixtures/enterprise.wsdl.xml';

        $serviceLocation = SFDC_SERVICE_LOCATION ? SFDC_SERVICE_LOCATION : null;

        $connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);

        $connection->login();

        return $connection;
    }

    private static function getClient()
    {
        return new EnterpriseClient(self::getConnection());
    }

    public function testConnection()
    {
        $client = self::getClient();

        $connection = $client->getConnection();

        $this->assertTrue($connection->isLoggedIn());
        $this->assertTrue($connection->getDebug());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult', $connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', $connection->getCredentials());
    }

    public function testConnectionInputHeaders()
    {
        $headers = self::getClient()->getConnection()->getSoapInputHeaders();

        $this->assertCount(2, $headers);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\SessionHeader', $headers[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\CallOptions', $headers[1]);

        $this->assertNotEmpty($headers[0]->getSessionId());
    }

    public function testLogout()
    {
        $connection = self::getClient()->getConnection();

        $connection->logout();

        $this->assertFalse($connection->isLoggedIn());
        $this->assertNull($connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', $connection->getCredentials());
    }

    public function testClient()
    {
        $client = self::getClient();

        $this->assertEquals('urn:enterprise.soap.sforce.com', $client->getUri());
        $this->assertEquals('26.0', $client->getAPIVersion()); // hard coded, should match wsdl. @todo: refactor me.
    }

    public function testDML()
    {
        $client = self::getClient();

        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse = $client->create($sobject);

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

        $updateResponse = $client->update($sobject);

        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $updateResponse);
        $this->assertNotEmpty(true, $updateResponse->get('result'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $updateResponse->get('result'));
        $this->assertNotCount(0, $updateResponse->get('result'));
        $this->assertEquals(1, $updateResponse->get('result')->get(0)->get('success'));

        $deleteResponse = $client->delete($createResponse->get('result')->get(0)->get('id'));

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
            self::getClient()->create($sobject);
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
        $response = self::getClient()->describeSobject('Contact');

        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty($response->get('result'));
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\DescribeSObjectResult', $response->get('result'));
    }

    public function testDescribeLayout()
    {
        $response = self::getClient()->describeLayout('Contact');

        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResult', $response);
        $this->assertNotEmpty($response->get('result'));
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\DescribeLayoutResult', $response->get('result'));
    }

    public function testQuery()
    {
        $client = self::getClient();

        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse= $client->create($sobject);

        $queryResponse = $client->query('SELECT Id, Salutation, Title, FirstName, LastName FROM Contact WHERE Id = \'' . $createResponse->get('result')->get(0)->get('id') . '\'');

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

        $client->delete($createResponse->get('result')->get(0)->get('id'));
    }

    public function testQueryNegative()
    {
        $exThrown = null;

        try
        {
            self::getClient()->query('FROM Dingsda SELECT Nix');
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
        $client = self::getClient();

        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse= $client->create($sobject);

        $client->delete($createResponse->get('result')->get(0)->get('id'));

        $queryResponse = $client->queryAll('SELECT Id, Salutation, Title, FirstName, LastName FROM Contact WHERE Id = \'' . $createResponse->get('result')->get(0)->get('id') . '\'');

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

