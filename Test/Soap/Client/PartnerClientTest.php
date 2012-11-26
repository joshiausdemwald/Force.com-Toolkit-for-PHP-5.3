<?php
namespace Codemitte\ForceToolkit\Test\Soap\Client;

use
    Codemitte\ForceToolkit\Soap\Client\PartnerClient,
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Mapping\Base\login,
    Codemitte\ForceToolkit\Soap\Mapping\Sobject
;

/**
 * @group PartnerClient
 */
class PartnerClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection
     */
    private static function getConnection()
    {
        $credentials = new login(SFDC_USERNAME, SFDC_PASSWORD);

        $wsdl = __DIR__ . '/../../fixtures/partner.wsdl.xml';

        $serviceLocation = SFDC_SERVICE_LOCATION ? SFDC_SERVICE_LOCATION : null;

        $connection = new SfdcConnection($credentials, $wsdl, $serviceLocation, array(), true);

        $connection->login();

        return $connection;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soap\Client\APIInterface
     */
    public static function getClient()
    {
        return new PartnerClient(self::getConnection());
    }

    public function testConnection()
    {
        $connection = self::getConnection();

        $this->assertTrue($connection->isLoggedIn());
        $this->assertTrue($connection->getDebug());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult', $connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', $connection->getCredentials());
    }

    public function testConnectionInputHeaders()
    {
        $client = self::getClient();

        $headers = $client->getConnection()->getSoapInputHeaders();

        $this->assertCount(2, $headers);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\SessionHeader', $headers[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Header\CallOptions', $headers[1]);

        $this->assertNotEmpty($headers[0]->getSessionId());
    }

    public function testLogout()
    {
        $client = self::getClient();

        $connection = $client->getConnection();

        $connection->logout();

        $this->assertFalse($connection->isLoggedIn());
        $this->assertNull($connection->getLoginResult());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Base\login', $connection->getCredentials());
    }

    public function testClient()
    {
        $client = self::getClient();

        $this->assertEquals('urn:partner.soap.sforce.com', $client->getUri());
        $this->assertEquals('26.0', $client->getAPIVersion()); // hard coded, should match wsdl. @todo: refactor me.
    }

    public function testClientToAny()
    {
        $obj = array(
            'testkey'=> 'testvalue',
            'testkey2__c' => 'testvalue2'
        );

        $target = new \stdClass();

        self::getClient()->toAny($obj, $target);

        $this->assertNotEmpty($target->any);

        $this->assertEquals('<testkey><![CDATA[testvalue]]></testkey><testkey2__c><![CDATA[testvalue2]]></testkey2__c>', $target->any);
    }

    public function testClientFromSobject()
    {
        $client = self::getClient();

        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $result = $client->fromSobject($sobject);

        $this->assertInstanceOf('\stdClass', $result);

        $this->assertEquals('Contact', $result->type);
        $this->assertEquals('<Salutation><![CDATA[Mr]]></Salutation><FirstName><![CDATA[Hans]]></FirstName><LastName><![CDATA[Wurst]]></LastName>', $result->any);
        $this->assertObjectHasAttribute('fieldsToNull', $result);
        $this->assertInstanceOf('\SoapVar', $result->fieldsToNull);

        // WEIRD... @todo: never change a running system, but consider taking a look.
        $this->assertEquals('<fieldsToNull>Title</fieldsToNull>', $result->fieldsToNull->enc_value->enc_value);
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

    /**
     * Tests insert, fetch, re-update (duplicate ID problem)
     */
    public function testReUpdateDML()
    {
        $client = self::getClient();

        $sobject = new Sobject('Contact', array(
            'Salutation' => 'Mr',
            'Title' => null,
            'FirstName' => 'Hans',
            'LastName' => 'Wurst'
        ));

        $createResponse = $client->create($sobject);

        $queryResponse = $client->query('SELECT Id, Salutation, Title, FirstName, LastName FROM Contact WHERE Id=\'' . $createResponse->get('result')->get(0)->get('id') . '\'');

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\QueryResult', $queryResponse->result);
        $this->assertEquals(1, $queryResponse->result->getSize());
        $toUpdate = $queryResponse->result->getRecords()->get(0);

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Sobject', $toUpdate);

        $toUpdate['FirstName'] = 'Updated firstname';

        $exThrown = null;

        $updateResult = null;

        try
        {
            $updateResponse = $client->update($toUpdate);
        }
        catch(\Exception $e)
        {
            $exThrown = $e;
        }

        $this->assertNull($exThrown);

        $this->assertNotNull($updateResponse);
        $this->assertNotEmpty($updateResponse->result);
        $this->assertCount(1, $updateResponse->result);
        $this->assertEquals($toUpdate['Id'], $updateResponse->result[0]->id);
        $this->assertEquals(1, $updateResponse->result[0]->success);
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
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\QueryResult', $queryResponse->get('result'));
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

    public function testQueryMore()
    {
        $client = self::getClient();

        $saveResponses = array();

        for($j = 0; $j < 5; $j++)
        {
            $sobjects = array();

            for($i = 0; $i < 200; $i ++)
            {
                $sobjects[] = new Sobject('Contact', array(
                    'Salutation' => 'Mr',
                    'Title' => 'lord',
                    'FirstName' => 'Firstname_' . $j . $i,
                    'LastName' => 'Lastname_' . $j . $i
                ));
            }
            $saveResponses[] = $client->create($sobjects);
        }

        $queryResponse = $client->query('SELECT Salutation, Title, FirstName, LastName FROM Contact', 750);

        $this->assertNotEmpty($queryResponse->get('result'));
        $this->assertNotEmpty($queryResponse->get('result')->get('queryLocator'));
        $this->assertNotEquals(true, $queryResponse->get('result')->get('done'));
        $this->assertGreaterThan(750, $queryResponse->get('result')->get('size'));

        $queryLocator = $queryResponse->get('result')->get('queryLocator');

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Type\QueryLocator', $queryLocator);
        $this->assertGreaterThan(0, (string)$queryLocator);

        $queryResponse = $client->queryMore($queryLocator);

        $this->assertNotEmpty($queryResponse->get('result'));
        $this->assertNotEmpty($queryResponse->get('result')->get('queryLocator'));
        $this->assertEquals(true, $queryResponse->get('result')->get('done'));
        $this->assertGreaterThan(750, $queryResponse->get('result')->get('size'));

        foreach($saveResponses AS $saveResponse)
        {
            $ids = array();

            foreach($saveResponse->get('result') AS $res)
            {
                $ids[] = $res->get('id');
            }
            $client->delete($ids);
        }
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
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\QueryResult', $queryResponse->get('result'));
        $this->assertNotCount(0, $queryResponse->get('result')->getRecords());
        $this->assertEquals(1, $queryResponse->get('result')->getSize());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\SobjectInterface', $queryResponse->get('result')->getRecords()->get(0));
        $this->assertEquals('Mr', $queryResponse->get('result')->getRecords()->get(0)->get('Salutation'));
        $this->assertEquals(null, $queryResponse->get('result')->getRecords()->get(0)->get('Title'));
        $this->assertEquals('Hans', $queryResponse->get('result')->getRecords()->get(0)->get('FirstName'));
        $this->assertEquals('Wurst', $queryResponse->get('result')->getRecords()->get(0)->get('LastName'));
    }
}

