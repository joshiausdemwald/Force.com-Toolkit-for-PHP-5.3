<?php
/**
 * Copyright (C) 2012 code mitte GmbH - Zeughausstr. 28-38 - 50667 Cologne/Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so, subject
 * to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Codemitte\Soap\Client\Connection;

use \Serializable;
use \SoapHeader;
use Codemitte\Soap\Hydrator\HydratorInterface;

/**
 * ConnectionInterface
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
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
     * @param \Codemitte\Soap\Hydrator\HydratorInterface $hydrator
     * @return mixed
     */
    public function soapCall($name, $args, HydratorInterface $hydrator = null);

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
     * @param array $headers
     * @param bool $permanent
     * @internal param array $header
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
    public function registerClass($complexType, $classname);

    /**
     * Adds a bunch of classes mapped by their filenames
     * to the classmap option.
     *
     * @abstract
     * @param $dirname
     */
    public function registerClassDir($dirname);

    /**
     * Adds types to the connection's soap typemap.
     *
     * @param string $typename
     * @param string $class
     * @param string $targetNamespace
     */
    public function registerType($simpleType, $class, $targetNamespace = null);

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
    
    /**
     * Returns the connection's default hydrator.
     *
     * @abstract
     *
     * @return HydratorInterface
     */
    public function getHydrator();
}
