<?php
namespace Codemitte\Soap\Client\Connection;

use \Serializable;
use \SoapHeader;

/**
 * ConnectionInterface
 */
interface ConnectionInterface extends Serializable
{
    /**
     * Performs a soap call to the given $name
     * action and the $args arguments list.
     *
     * $args may be an array with named key-value-
     * pairs or a popo (plain old php object).
     *
     * @abstract
     * @param $name
     * @param mixed $args
     * @return mixed
     */
    public function soapCall($name, $args);

    /**
     * getWsdl()
     *
     * @abstract
     * @return string
     */
    public function getWsdl();

    /**
     * setWsdl
     *
     * @abstract
     * @param string $wsdl
     */
    public function setWsdl($wsdl);

    /**
     * Sets the location of the webservice.
     * This corresponds to the "location" option
     * that can be set by calling "setOption('location')".
     *
     * Optional in wsdl mode.
     *
     * @abstract
     * @param string $location
     */
    public function setLocation($location);

    /**
     * Returns the webservice location if defined
     * or null when in wsdl mode (in this case
     * the location will be introspected).
     *
     * @abstract
     *
     * @return string $location
     */
    public function getLocation();

    /**
     * Sets the target namespace of the webservice if in
     * non-wsdl-mode, overrides in wsdl-mode.
     *
     * @abstract
     *
     * @param $uri
     *
     * @return void
     */
    public function setURI($uri);

    /**
     * Returns the target namespace of the webservice (if
     * defined, otherwise NULL - it then will be introspected
     * by the soap client).
     *
     * @abstract
     *
     * @return string
     */
    public function getURI();

    /**
     * Overrides and sets per-request or permanent soap input headers,
     * dependent on the $permanent flag.
     *
     * @abstract
     * @param array $header
     * @param bool $permanent
     * @return void
     */
    public function setSoapInputHeaders(array $headers, $permanent = false);

    /**
     * Add SOAP input header
     *
     * @param SoapHeader $header
     * @param bool $permanent
     */
    public function addSoapInputHeader(SoapHeader $header, $permanent = false);

    /**
     * Returns the soap input headers. If $permanent is momitted, both
     * permanent and temporary soap headers will be returned.
     * If $permanent is true, only permanent soap input headers will be returned,
     * otherwise only the temporary ones.
     *
     *
     * @param bool $permanent
     *
     * @return array
     */
    public function getSoapInputHeaders($permanent = null);

    /**
     * Reset SOAP input headers
     *
     * @return void
     */
    public function resetSoapInputHeaders();

    /**
     * Retrieve request XML
     *
     * @return string
     */
    public function getLastRequest();

    /**
     * Get response XML
     *
     * @return string
     */
    public function getLastResponse();

    /**
     * Retrieve request headers
     *
     * @return string
     */
    public function getLastRequestHeaders();

    /**
     * Retrieve response headers (as string)
     *
     * @return string
     */
    public function getLastResponseHeaders();

    /**
     * Return a list of available functions
     *
     * @return array
     * @throws Zend_Soap_Client_Exception
     */
    public function getFunctions();

    /**
     * Return a list of SOAP types
     *
     * @return array
     * @throws Zend_Soap_Client_Exception
     */
    public function getTypes();

    /**
     * setCookie()
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function setCookie($name, $value= null);

    /**
     * Adds classes to the connection's soap classmap.
     *
     * @abstract
     *
     * @param string $type
     * @param string $classname
     */
    public function registerClass($type, $classname);

    /**
     * Adds types to the connection's soap typemap.
     *
     * @param string $typename
     * @param string $class
     * @param string $targetNamespace
     */
    public function registerType($typename, $class, $targetNamespace = null);

    /**
     * setOption()
     *
     * @abstract
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value);

    /**
     * getOption()
     *
     * @abstract
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null);

    /**
     * getOptions()
     *
     * @abstract
     * @return array
     */
    public function getOptions();

    /**
     * setOptions()
     *
     * @abstract
     * @param array $options
     */
    public function setOptions(array $options);
}
