<?php
namespace Codemitte\Sfdc\Soap\Client;

use \Serializable;

/**
 * SoapClientInterface
 *
 * @interface
 * @abstract
 */
interface ClientInterface extends Serializable
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

    /**
     * Returns the API version the client implementation
     * fits to.
     *
     * @abstract
     *
     * @return string
     */
    public function getAPIVersion();
}
