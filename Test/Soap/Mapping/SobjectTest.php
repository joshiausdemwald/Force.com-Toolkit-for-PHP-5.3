<?php
namespace Codemitte\ForceToolkit\Test\Soap\Mapping;

use
    Codemitte\ForceToolkit\Soap\Mapping\Sobject,
    Codemitte\Soap\Mapping\GenericResultCollection
;

/**
 * @group Sobject
 */
class SobjectTest extends \PHPUnit_Framework_TestCase
{
    public function testSobjectSimpleAccess()
    {
        $sobject = new Sobject('Contact', array(
            'Id' => '003C000001PeRT2IAN',
            'hans' => 'wurst',
            'test' => 'testvalue'
        ));

        $this->assertCount(3, $sobject);

        $this->assertTrue($sobject->contains('hans'));
        $this->assertEquals('wurst', $sobject->get('hans'));
        $this->assertTrue(isset($sobject['test'])); // array_key_access incompatible to ArrayAccess interface
        $this->assertEquals('testvalue', $sobject['test']);
        $this->assertTrue(isset($sobject->test));
        $this->assertEquals('testvalue', $sobject->test);
        $this->assertNotNull($sobject->getId());
        $this->assertEquals('003C000001PeRT2IAN', $sobject->getId());
        $this->assertNotNull($sobject->getSobjectType());
        $this->assertEquals('Contact', $sobject->getSobjectType());

        $keys = $sobject->getKeys();
        $this->assertCount(3, $keys);
        $this->assertContains('Id', $keys);
        $this->assertContains('hans', $keys);
        $this->assertContains('test', $keys);
        $this->assertNotContains('type', $keys);
        $this->assertNotContains('_keyCache', $keys);
        $this->assertNotContains('_valueCache', $keys);
    }

    public function testArrayConversion()
    {
        $sobject = new Sobject('Contact', array(
            'Id' => '003C000001PeRT2IAN',
            'hans' => 'wurst',
            'test' => 'testvalue'
        ));

        $keys = $sobject->getKeys();
        $this->assertCount(3, $keys);

        $sobject->put('test', 'override1');
        $sobject->put('test2', 'testvalue2');

        $keys = $sobject->getKeys();
        $this->assertCount(4, $keys);

        $this->assertContains('Id', $keys);
        $this->assertContains('hans', $keys);
        $this->assertContains('test', $keys);
        $this->assertContains('test2', $keys);

        $this->assertNotContains('type', $keys);
        $this->assertNotContains('_keyCache', $keys);
        $this->assertNotContains('_valueCache', $keys);

        $array = $sobject->toArray();

        $this->assertCount(4, $array);
        $this->assertArrayHasKey('Id', $array);
        $this->assertArrayHasKey('hans', $array);
        $this->assertArrayHasKey('test', $array);
        $this->assertArrayHasKey('test2', $array);

        $this->assertEquals('003C000001PeRT2IAN', $array['Id']);
        $this->assertEquals('wurst', $array['hans']);
        $this->assertEquals('override1', $array['test']);
        $this->assertEquals('testvalue2', $array['test2']);

        $sobject->remove('hans');

        $keys = $sobject->getKeys();

        $this->assertCount(3, $keys);
        $this->assertCount(3, $sobject); // Countable
    }

    public function testSobjectAny()
    {
        $sobject = new Sobject('Contact', array(
            'Id' => '003C000001PeRT2IAN'
        ));
        $sobject->any = '<sf:test>testvalue</sf:test><sf:hans>wurst</sf:hans>';
        $this->assertTrue($sobject->contains('hans'));
        $this->assertEquals('wurst', $sobject->get('hans'));
        $this->assertTrue(isset($sobject['test'])); // array_key_access incompatible to ArrayAccess interface
        $this->assertEquals('testvalue', $sobject['test']);
        $this->assertNotNull($sobject->getId());
        $this->assertEquals('003C000001PeRT2IAN', $sobject->getId());
        $this->assertNotNull($sobject->getSobjectType());
        $this->assertEquals('Contact', $sobject->getSobjectType());
    }

    public function testSobjectAnyParent()
    {
        $sobject = new Sobject('Contact', array(
            'Id' => '003C000001PeRT2IAN'
        ));
        $sobject->any = array(
            0 => '<sf:test>testvalue</sf:test><sf:hans>wurst</sf:hans>',
            'Account__r' => new Sobject('Account', array(
                'Id' => '002C000001PeRT2IAN',
                'Name' => 'Testcompany'
            ))
        );

        $this->assertTrue($sobject->contains('hans'));
        $this->assertEquals('wurst', $sobject->get('hans'));
        $this->assertTrue(isset($sobject['test'])); // array_key_access incompatible to ArrayAccess interface
        $this->assertEquals('testvalue', $sobject['test']);
        $this->assertNotNull($sobject->getId());
        $this->assertEquals('003C000001PeRT2IAN', $sobject->getId());
        $this->assertNotNull($sobject->getSobjectType());
        $this->assertEquals('Contact', $sobject->getSobjectType());
        $this->assertTrue($sobject->contains('Account__r'));
        $this->assertTrue(isset($sobject['Account__r']));
        $this->assertTrue(property_exists($sobject, 'Account__r'));

        $account = $sobject['Account__r'];

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Sobject', $account);
        $this->assertEquals('Account', $account->getSobjectType());
        $this->assertNotNull($account->getId());
        $this->assertTrue($account->contains('Name'));
        $this->assertEquals('Testcompany', $account->Name);

    }

    public function testSobjectRelated()
    {
        $sobject = new Sobject('Account', array(
            'Id' => '003C000001PeRT2IAN'
        ));
        $sobject->any = array(
            0 => '<sf:test>testvalue</sf:test><sf:hans>wurst</sf:hans>',
            'Contacts' => array(new Sobject('Account', array(
                'Id' => '002C000001PeRT2IAN',
                'Name' => 'Testcompany'
            )))
        );
        $this->assertTrue($sobject->contains('Contacts'));
        $this->assertInstanceOf('\Codemitte\Soap\Mapping\GenericResultCollection', $sobject->get('Contacts'));
        $this->assertCount(1, $sobject->get('Contacts'));

        $contact1 = $sobject->get('Contacts')->get(0);

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soap\Mapping\Sobject', $contact1);
        $this->assertNotNull($contact1->getId());
        $this->assertEquals('002C000001PeRT2IAN', $contact1->getId());
        $this->assertTrue($contact1->contains('Name'));
        $this->assertEquals('Testcompany', $contact1->get('Name'));
    }

    public function testGetFieldsToNull()
    {
        $sobject = new Sobject('Account', array(
            'Name' => 'Firmax',
            'BillingStreet' => null
        ));

        $this->assertNotEmpty($sobject->getFieldsToNull());
        $this->assertCount(1, $sobject->getFieldsToNull());
        $this->assertContains('BillingStreet', $sobject->getFieldsToNull());
    }

    public function testSerialize()
    {
        $sobject = new Sobject('Account', array(
            'Name' => 'Firmax',
            'BillingStreet' => 'Dingsstraße',
            'BillingHouseNumber' => NULL,
            'Contacts' => new GenericResultCollection(array(
                new sObject('Contact', array(
                    'FirstName' => 'Luise',
                    'LastName' => 'Kaffeebohne'
                )),
                new sObject('Contact', array(
                    'FirstName' => 'Petra',
                    'LastName' => 'Artep'

                ))
            ))
        ));

        $serialized = serialize($sobject);

        $sobject = unserialize($serialized);

        $this->assertEquals('Firmax', $sobject->Name);
        $this->assertEquals('Dingsstraße', $sobject->BillingStreet);
        $this->assertInstanceOf('Codemitte\Soap\Mapping\GenericResultCollection', $sobject->Contacts);
        $this->assertEquals('Luise', $sobject->Contacts[0]['FirstName']);
        $this->assertEquals('Kaffeebohne', $sobject->Contacts[0]['LastName']);
        $this->assertEquals('Petra', $sobject->Contacts[1]['FirstName']);
        $this->assertEquals('Artep', $sobject->Contacts[1]['LastName']);

        $this->assertNull($sobject->BillingHouseNumber);
        $this->assertNotEmpty($sobject->getFieldsToNull());
        $this->assertCount(1, $sobject->getFieldsToNull());
        $this->assertContains('BillingHouseNumber', $sobject->getFieldsToNull());
    }
}
