<?php
namespace Codemitte\Sfdc\Soap\Client;

use Codemitte\Sfdc\Soap\Client\Connection\SfdcConnectionInterface;

/**
 * EnterpriseClient
 */
class EnterpriseClient extends BaseClient
{
    /**
     * @param SfdcConnectionInterface $connection
     */
    public function configure(SfdcConnectionInterface $connection)
    {
        $connection->registerClass('DescribeLayout', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayout');
        $connection->registerClass('DescribeLayoutButton', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutButton');
        $connection->registerClass('DescribeLayoutButtonSection', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutButtonSection');
        $connection->registerClass('DescribeLayoutComponent', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutComponent');
        $connection->registerClass('DescribeLayoutItem', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutItem');
        $connection->registerClass('describeLayoutResponse', 'Codemitte\\Sfdc\\Soap\\Mapping\\describeLayoutResponse');
        $connection->registerClass('DescribeLayoutResult', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutResult');
        $connection->registerClass('DescribeLayoutRow', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutRow');
        $connection->registerClass('DescribeLayoutSection', 'Codemitte\\Sfdc\\Soap\\Mapping\\DescribeLayoutSection');

        $connection->registerType('ID', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\ID');
        $connection->registerType('QueryLocator', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\QueryLocator');
        $connection->registerType('StatusCode', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\StatusCode');
        $connection->registerType('fieldType', 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\fieldType');
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

    /**
     * describeLayout()
     *
     * @param string $sObjectType
     * @param array $recordTypeIds
     * @return \Codemitte\Sfdc\Soap\Mapping\describeLayoutResponse
     */
    public function describeLayout($sObjectType, array $recordTypeIds = null)
    {
        return $this->getConnection()->__call(
            'describeLayout',
            array(
                 array(
                     'sObjectType' => $sObjectType,
                     'recordTypeIds' => $recordTypeIds
                 )
            )
        );
    }

    /**
     * describeSobject()
     *
     * @param string $sObjectType
     *
     * @internal param array $recordTypeIds
     *
     * @return describeSObjectResponse
     */
    public function describeSobject($sObjectType)
    {
        return $this->getConnection()->__call(
            'describeSobject',
            array(
                 array(
                     'sObjectType' => $sObjectType
                 )
            )
        );
    }
}

