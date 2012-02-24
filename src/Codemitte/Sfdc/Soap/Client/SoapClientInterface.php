<?php
namespace Codemitte\Sfdc\Soap\Client;

/**
 * SoapClientInterface
 *
 * @interface
 * @abstract
 */
interface SoapClientInterface
{
    /**
     * Returns the Connection to the soap service as an extension
     * of Zend_Soap_Client.
     *
     * @abstract
     * @return Connection
     */
    public function getConnection();

    /**
     * Returns the TargetNamespace as a valid uri string.
     *
     * @abstract
     * @return string $uri
     */
    public function getUri();
}
