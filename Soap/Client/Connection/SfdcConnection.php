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

namespace Codemitte\ForceToolkit\Soap\Client\Connection;

use Codemitte\Soap\Client\Connection\SoapClientCommon;
use Codemitte\Soap\Client\Connection\Connection;
use Codemitte\ForceToolkit\Soap\Client\Connection\Hydrator\SfdcResultHydrator;
use Codemitte\Soap\Client\Exception AS ClientException;
use Codemitte\ForceToolkit\Soap\Mapping\Base\login;
use Codemitte\ForceToolkit\Soap\Header\SessionHeader;
use Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult;

/**
 * SfdcConnection: Sfdc soap connector.
 *
 * Options:
 * An array of options. If working in WSDL mode, this parameter is optional. If working in non-WSDL mode, the "location"
 * and "uri" options must be set, where location is the URL of the SOAP server to send the request to, and "uri" is the
 * target namespace of the SOAP service.
 *
 * The "style" and "use" options only work in non-WSDL mode. In WSDL mode, they come from the WSDL file.
 *
 * Available options:
 *  - "deserialize_as_array": Deserializes SOAP-Responses as native arrays rather as instances of \stdClass
 *  - "trace": Trace soap requests and responses so that methods like "getLastResponse()" are enabled to be used and
 *             faults may be backtraced.
 *  - "encoding": The encoding option defines internal character encoding. This option does not change the encoding of
 *                SOAP requests (it is always utf-8), but converts strings into it.
 *  - "keep_alive": The keep_alive option is a boolean value defining whether to send the Connection: Keep-Alive header
 *                  or Connection: close.
 *  - "soap_version": The soap_version option specifies whether to use SOAP 1.1 (default), or SOAP 1.2 client. Use on of
 *                    the SOAP_X_Y-constants.
 *  - "compression": For example gzip compression. Code sample: <code>
 *                   $options = array(
 *                       'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 9 // 9 is the gzip level
 *                   );</code>.
 *  - "exceptions": If set to false, soapCall() returns an instance of \SoapFault in the case of an error. Otherwise, an
 *                  instance of \SoapFault will be thrown as an exception.
 *  - "connection_timeout": The connection_timeout option defines a timeout in seconds for the connection to the SOAP
 *                          service. This option does not define a timeout for services with slow responses. To limit
 *                          the time to wait for calls to finish the default_socket_timeout setting is available.
 *  - "cache_wsdl": The cache_wsdl option is one of WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY or
 *                  WSDL_CACHE_BOTH.
 *  - "user_agent": The user_agent option specifies string to use in User-Agent header.
 *  - "stream_context": The stream_context option is a resource for context. Example: <code>
 *                           $socket_context = stream_context_create(
 *                                           array('http' =>
 *                                              array(
 *                                                  'protocol_version'  => 1.0
 *                                              )
 *                                          )
 *                           );
 *                           new SoapClient([...],array('stream_context' => $socket_context));</code>
 * - "features": The features option is a bitmask of SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS.
 *
 * For HTTP authentication, the login and password options can be used to supply credentials.
 *
 * For making an HTTP connection through a proxy server, the options "proxy_host", "proxy_port", "proxy_login" and
 * "proxy_password" are also available. For HTTPS client certificate authentication use "local_cert" and "passphrase"
 * options. An authentication may be supplied in the authentication option. The authentication method may be
 * either SOAP_AUTHENTICATION_BASIC (default) or SOAP_AUTHENTICATION_DIGEST.
 *  - "login": The HTTP auth username.
 *  - "password": The HTTP auth password.
 *  - "proxy_host" The proxy hostname.
 *  - "proxy_port" The proxy hosts's port
 *  - "proxy_login: The proxy auth username
 *  - "proxy_password: The proxy auth password
 *  - "local_cert":
 *  - "passphrase"
 *
 * PHP SOAP Extension's .ini Options:
 *   -  soap.wsdl_cache_enabled	1	        aktiviert oder deaktiviert den WSDL-Cache
 *   -  soap.wsdl_cache_dir	    /tmp	    Verzeichnis für den WSDL-Cache
 *   -  soap.wsdl_cache_ttl	    86400	    Zeitraum in Sekunden wie lange zwischengespeicherte WSDL-Dateien genutzt werden sollen
 *   -  soap.wsdl_cache_limit	    5	    maximale Anzahl von WSDL-Dateien, die gespeichert werden können
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
class SfdcConnection extends Connection implements SfdcConnectionInterface
{
    /**
     * Version string to append to the user agent.
     */
    const VERSION = 'v0.0.1Beta';

    const
        OPENSSL_VERSION_0 = 1,
        OPENSSL_VERSION_1 = 2;

    /**
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult
     */
    private $loginResult;

    /**
     * @var \Codemitte\ForceToolkit\Soap\Mapping\Base\login
     */
    private $credentials;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var int
     */
    private $lastLoginTime;

    /**
     * Constructor.
     *
     * Options:
     * An array of options. If working in WSDL mode, this parameter is optional. If working in non-WSDL mode, the "location"
     * and "uri" options must be set, where location is the URL of the SOAP server to send the request to, and "uri" is the
     * target namespace of the SOAP service.
     *
     * The "style" and "use" options only work in non-WSDL mode. In WSDL mode, they come from the WSDL file.
     *
     * Available options:
     *  - "deserialize_as_array": Deserializes SOAP-Responses as native arrays rather as instances of \stdClass
     *  - "trace": Trace soap requests and responses so that methods like "getLastResponse()" are enabled to be used and
     *             faults may be backtraced.
     *  - "encoding": The encoding option defines internal character encoding. This option does not change the encoding of
     *                SOAP requests (it is always utf-8), but converts strings into it.
     *  - "keep_alive": The keep_alive option is a boolean value defining whether to send the Connection: Keep-Alive header
     *                  or Connection: close.
     *  - "soap_version": The soap_version option specifies whether to use SOAP 1.1 (default), or SOAP 1.2 client. Use on of
     *                    the SOAP_X_Y-constants.
     *  - "compression": For example gzip compression. Code sample: <code>
     *                   $options = array(
     *                       'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 9 // 9 is the gzip level
     *                   );</code>.
     *  - "exceptions": If set to false, soapCall() returns an instance of \SoapFault in the case of an error. Otherwise, an
     *                  instance of \SoapFault will be thrown as an exception.
     *  - "connection_timeout": The connection_timeout option defines a timeout in seconds for the connection to the SOAP
     *                          service. This option does not define a timeout for services with slow responses. To limit
     *                          the time to wait for calls to finish the default_socket_timeout setting is available.
     *  - "cache_wsdl": The cache_wsdl option is one of WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY or
     *                  WSDL_CACHE_BOTH.
     *  - "user_agent": The user_agent option specifies string to use in User-Agent header.
     *  - "stream_context": The stream_context option is a resource for context. Example: <code>
     *                           $socket_context = stream_context_create(
     *                                           array('http' =>
     *                                              array(
     *                                                  'protocol_version'  => 1.0
     *                                              )
     *                                          )
     *                           );
     *                           new SoapClient([...],array('stream_context' => $socket_context));</code>
     * - "features": The features option is a bitmask of SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS.
     *
     * For HTTP authenication, the login and password options can be used to supply credentials.
     *
     * For making an HTTP connection through a proxy server, the options "proxy_host", "proxy_port", "proxy_login" and
     * "proxy_password" are also available. For HTTPS client certificate authentication use "local_cert" and "passphrase"
     * options. An authentication may be supplied in the authentication option. The authentication method may be
     * either SOAP_AUTHENTICATION_BASIC (default) or SOAP_AUTHENTICATION_DIGEST.
     *  - "login": The HTTP auth username.
     *  - "password": The HTTP auth password.
     *  - "proxy_host" The proxy hostname.
     *  - "proxy_port" The proxy hosts's port
     *  - "proxy_login: The proxy auth username
     *  - "proxy_password: The proxy auth password
     *  - "local_cert":
     *  - "passphrase"
     *
     * - openssl_version: One of the OPENSSL_VERSION_x-constants. If a version >= 0 is chosed, a request workaround
     *   if performed to exclude SSLv2 from beeing used as cipher.
     *
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Base\login $credentials
     * @param string $wsdl: The path to the wsdl file.
     * @param string $serviceLocation: The location of the webservice, only used when differs from service definition
     *                                 in wsdl.
     * @param array $options
     * @param bool $debug
     */
    public function __construct(
        login $credentials,
        $wsdl,
        $serviceLocation = null,
        array $options = array(),
        $debug = false
    ) {
        // $wsdl = null, array $options = array(), HydratorInterface $hydrator = null
        parent::__construct($wsdl, array(), new SfdcResultHydrator, null);

        $this->credentials = $credentials;

        if(null !== $serviceLocation)
        {
            $this->setOption('location', $serviceLocation);
        }

        $this->debug = $debug;

        $this->setOptions(array_merge($options, array(
            'soap_version' => SOAP_1_1,
            'user_agent' => 'salesforce-toolkit-php53/' . self::VERSION,
            'encoding' => 'utf-8',
            'trace' => $debug ? true : false,
            'exceptions' => true,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'openssl_version' => self::OPENSSL_VERSION_0,
            'keep_alive' => true,

            // PREPARE STREAM CONTEXT OPTIONS:
            // Salesforce.com supports only the Secure Sockets Layer (SSL) protocol SSLv3 and the Transport Layer
            // Security (TLS) protocol. Ciphers must have a key length of at least 128 bits.
            // context ssl: http://www.php.net/manual/en/context.ssl.php
            /*'stream_context'  => stream_context_create(array('ssl' => array(
                    'ciphers' => 'ALL:!SSLv2:+TLSv1:+SSLv3', 	// DISABLE SSLv2
            //        'verify_peer' => false,		// TESTING: DO NOT VALIDATE PEER CERT

                )
            )),*/

            // IF DEBUG
            'cache_wsdl' => $debug ? WSDL_CACHE_NONE : WSDL_CACHE_MEMORY

            //'cache_wsdl' => WSDL_CACHE_NONE

            //'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        )));

        $this->registerClass('GetUserInfoResult', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\GetUserInfoResult');
        $this->registerClass('login', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\login');
        $this->registerClass('LoginFault', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\LoginFault');
        $this->registerClass('loginResponse', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\loginResponse');
        $this->registerClass('LoginScopeHeader', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\LoginScopeHeader');
        $this->registerClass('LoginResult', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\LoginResult');
        $this->registerClass('SessionHeader', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\SessionHeader');
        $this->registerClass('CallOptions', 'Codemitte\\ForceToolkit\\Soap\\Mapping\\Base\\CallOptions');
    }

    /**
     * login()
     *
     * Registers the given username to the specified organisation.
     *
     * @internal param $credentials
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Base\loginResponse
     */
    public function login()
    {
        $response = parent::soapCall('login', array($this->getCredentials()));

        $this->setLoginResult($response->getResult());

        $this->setOption('location', $this->loginResult->getServerUrl());

        $this->lastLoginTime = time();

        return $response;
    }

    /**
     * Returns the login result. Expected to be null unless
     * login() has been called.
     *
     * @return LoginResult
     */
    public function getLoginResult()
    {
        return $this->loginResult;
    }

    /**
     * Returns true if a login result exists.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return null !== $this->loginResult;
    }

    /**
     * @throws \LogicException
     */
    public function logout()
    {
        if( ! $this->isLoggedIn())
        {
            throw new \LogicException('Cannot logout a non-logged-in user.');
        }

        $this->soapCall('logout', array());

        $this->loginResult = null;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        return serialize(array(
            'credentials'   => $this->credentials,
            'loginResult'   => $this->loginResult,
            'debug'         => $this->debug,
            'lastLoginTime' => $this->lastLoginTime,
            '__parentData'  => parent::serialize()
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->credentials   = $data['credentials'];
        $this->loginResult   = $data['loginResult'];
        $this->debug         = $data['debug'];
        $this->lastLoginTime = $data['lastLoginTime'];

        parent::unserialize($data['__parentData']);
    }

    /**
     * doRequestCallback()
     *
     * Note on ID Fields: The ID element is repeated to make programatic use of the partner API easier.
     * The WSDL defines a common ID element in the base SObject definition, in addition, for query, we'll
     * put every element you specified in your query in the any collection, this makes it easy to index into
     * the any collection to find a field value based on your select list. That does mean for queries that
     * select the Id field it'll appear twice, once in the base sobject defintion and a 2nd time in the any
     * collection.
     *
     * @internal
     * @param SoapClientCommon $client
     * @param $request
     * @param $location
     * @param $action
     * @param $version
     * @param null $one_way
     * @return mixed
     */
    public function doRequestCallback(SoapClientCommon $client, $request, $location, $action, $version, $one_way = null)
    {
        if($this->getOption('openssl_version') === self::OPENSSL_VERSION_1)
        {
            $callable = array($client, '__doRequestFallback');

            $args = array(
                $request,
                $location,
                $action,
                $version
            );

            // Perform request as is
            if (null !== $one_way)
            {
                $args[] = $one_way;
            }

            return call_user_func_array($callable, $args);
        }
        return parent::doRequestCallback($client, $request, $location, $action, $version, $one_way);
    }

    /**
     * normalizeOption()
     *
     * @throws ClientException\UnknownOptionException
     * @param string $key
     * @return string $normalizedKey
     */
    protected function normalizeOption($key)
    {
        try
        {
            return parent::normalizeOption($key);
        }
        catch(ClientException\UnknownOptionException $e)
        {
            switch($key)
            {
                case 'openssl_version':
                case 'opensslVersion':
                    return 'openssl_version';
                    break;
                default:
                    throw $e;
            }
        }
    }

    /**
     * To inject the session id of another connection
     * into this one.
     *
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult $loginResult
     * @return mixed
     */
    public function setLoginResult(LoginResult $loginResult)
    {
        $this->loginResult = $loginResult;
    }

    /**
     * @return login
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @return int
     */
    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }
}