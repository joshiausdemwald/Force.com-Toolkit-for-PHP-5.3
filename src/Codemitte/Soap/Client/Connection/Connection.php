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

use \SoapHeader;
use \SoapFault AS GenericSoapFault;

use Codemitte\Soap\Client\Connection\SoapFault;
use Codemitte\Soap\Hydrator;
use Codemitte\Soap\Client\Decorator\DecoratorInterface;
use Codemitte\Soap\Client\Decorator\SoapParamDecorator;
use Codemitte\Soap\Client\Exception AS ClientException;

/**
 * Connection: Generic SOAP Client connection class.
 *
 * Options:
 * An array of options. If working in WSDL mode, this parameter is optional. If working in non-WSDL mode, the "location"
 * and "uri" options must be set, where location is the URL of the SOAP server to send the request to, and "uri" is the
 * target namespace of the SOAP service.
 *
 * The "style" and "use" options only work in non-WSDL mode. In WSDL mode, they come from the WSDL file.
 *
 * Available options:
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
 *
 * PHP SOAP Extension's .ini Options:
 *   -  soap.wsdl_cache_enabled	1	        aktiviert oder deaktiviert den WSDL-Cache
 *   -  soap.wsdl_cache_dir	    /tmp	    Verzeichnis für den WSDL-Cache
 *   -  soap.wsdl_cache_ttl	    86400	    Zeitraum in Sekunden wie lange zwischengespeicherte WSDL-Dateien genutzt werden sollen
 *   -  soap.wsdl_cache_limit	    5	        maximale Anzahl von WSDL-Dateien, die gespeichert werden können
 *
 * Derived from Zend_Soap_Client (@copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com))
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
 */
class Connection implements ConnectionInterface
{
    const CLASS_MAP_INTERFACE = 'Codemitte\\Soap\\Mapping\\ClassInterface';

    const TYPE_MAP_INTERFACE = 'Codemitte\\Soap\\Mapping\\Type\\TypeInterface';

    const DEFAULT_OPTION_ENCODING = 'utf-8';

    const DEFAULT_OPTION_TRACE = true;

    const DEFAULT_OPTION_EXCEPTIONS = true;

    const DEFAULT_OPTION_CACHE_WSDL = WSDL_CACHE_MEMORY;

    const DEFAULT_OPTION_USER_AGENT = 'Force.com-Toolkit-For-PHP5.3/v0.0.1Beta';

    const DEFAULT_OPTION_KEEP_ALIVE = true;

    /**
     * @var SoapClientCommon
     */
    protected $soapClient;

	/**
     * @var array
     */
    private $options = array();

    /**
     * @var array
     */
    private $classMap = array();

    /**
     * @var array
     */
    private $typeMap = array();

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var array
     */
    private $permanentSoapInputHeaders   = array();

    /**
     * @var array
     */
    private $soapInputHeaders            = array();

    /**
     * @var array
     */
    private $soapOutputHeaders           = array();

    /**
     * @var Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @var DecoratorInterface
     */
    private $decorator;

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
     * @param string $wsdl
     * @param array $options
     * @param Hydrator\HydratorInterface $hydrator
     * @param DecoratorInterface|null $decorator
     */
    public function __construct(
        $wsdl = null,
        array $options = array(),
        Hydrator\HydratorInterface $hydrator = null,
        DecoratorInterface $decorator = null
    ) {
        $this->wsdl = $wsdl;

        $this->setOptions($options);

        if(null === $hydrator)
        {
            $hydrator = new Hydrator\ResultHydrator();
        }

        if(null === $decorator)
        {
            $decorator = new SoapParamDecorator($this->getURI());
        }

        $this->hydrator = $hydrator;

        $this->decorator = $decorator;

        $this->configure($options);
    }

    /**
     * Stub to perform additional configuration in child classes.
     *
     * @param array $options
     *
     * @return void
     */
    protected function configure(array $options)
    {

    }

    /**
     * Sets the location of the webservice.
     * This corresponds to the "location" option
     * that can be set by calling "setOption('location')".
     *
     * Optional in wsdl mode.
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->setOption('location', $location);
    }

    /**
     * Returns the webservice location if defined
     * or null when in wsdl mode (in this case
     * the location will be introspected).
     *
     *
     * @return string $location
     */
    public function getLocation()
    {
        return $this->getOption('location');
    }

    /**
     * Sets the target namespace of the webservice if in
     * non-wsdl-mode, overrides in wsdl-mode.
     *
     *
     * @param $uri
     *
     * @return void
     */
    public function setURI($uri)
    {
        $this->setOption('uri', $uri);
    }

    /**
     * Returns the target namespace of the webservice (if
     * defined, otherwise NULL - it then will be introspected
     * by the soap client).
     *
     *
     * @return string
     */
    public function getURI()
    {
        return $this->getOption('uri');
    }

    /**
     * setWsdl()
     *
     * @param $wsdl
     */
    public function setWsdl($wsdl)
    {
        $this->soapClient = null;

        $this->wsdl = $wsdl;
    }

    /**
     * getWsdl()
     *
     * @return string
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }

    /**
     * getOptions()
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * setOptions()
     *
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->soapClient = null;

        $this->options = array_merge($this->options, $this->normalizeOptions($options));
    }

    /**
     * setOption()
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value)
    {
        $this->soapClient = null;

        $this->options[$this->normalizeOption($key)] = $value;
    }

    /**
     * getOption()
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        $key = $this->normalizeOption($key);

        if(array_key_exists($key, $this->options))
        {
            return $this->options[$key];
        }
        return $default;
    }

    /**
     * Overrides and sets per-request or permanent soap input headers,
     * dependent on the $permanent flag.
     *
     * @param array $headers
     * @param bool $permanent
     *
     * @return void
     */
    public function setSoapInputHeaders(array $headers, $permanent = false)
    {
        if($permanent)
        {
            $this->permanentSoapInputHeaders = $headers;
        }
        else
        {
            $this->soapInputHeaders = $headers;
        }
    }

    /**
     * Add SOAP input header
     *
     * @param SoapHeader $header
     * @param bool $permanent
     *
     * @return void
     */
    public function addSoapInputHeader(SoapHeader $header, $permanent = false)
    {
        if ($permanent)
        {
            $this->permanentSoapInputHeaders[] = $header;
        }
        else
        {
            $this->soapInputHeaders[] = $header;
        }
    }

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
    public function getSoapInputHeaders($permanent = null)
    {
        if(null === $permanent)
        {
            return array_merge($this->permanentSoapInputHeaders, $this->soapInputHeaders);
        }

        if(false === $permanent)
        {
            return $this->soapInputHeaders;
        }

        return $this->permanentSoapInputHeaders;
    }

    /**
     * Reset SOAP input headers
     *
     * @return void
     */
    public function resetSoapInputHeaders()
    {
        $this->permanentSoapInputHeaders = array();

        $this->soapInputHeaders = array();
    }

    /**
     * Get last SOAP output headers.
     *
     * @return array
     */
    public function getSoapOutputHeaders()
    {
        return $this->soapOutputHeaders;
    }

    /**
     * Retrieve request XML
     *
     * @throws ClientException\MissingOptionException
     * @return string
     */
    public function getLastRequest()
    {
        if( ! $this->getOption('trace', self::DEFAULT_OPTION_TRACE))
        {
            throw new ClientException\MissingOptionException('getLastRequest() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastRequest();
    }

    /**
     * Get response XML
     *
     * @throws ClientException\MissingOptionException
     *
     * @return string
     */
    public function getLastResponse()
    {
        if( ! $this->getOption('trace', self::DEFAULT_OPTION_TRACE))
        {
            throw new ClientException\MissingOptionException('getLastResponse() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastResponse();
    }

    /**
     * Retrieve request headers
     *
     * @throws ClientException\MissingOptionException
     *
     * @return string
     */
    public function getLastRequestHeaders()
    {
        if( ! $this->getOption('trace'))
        {
            throw new ClientException\MissingOptionException('getLastRequestHeaders() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastRequestHeaders();
    }

    /**
     * Retrieve response headers (as string)
     *
     * @throws ClientException\MissingOptionException
     *
     * @return string
     */
    public function getLastResponseHeaders()
    {
        if( ! $this->getOption('trace'))
        {
            throw new ClientException\MissingOptionException('getLastResponseHeaders() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastResponseHeaders();
    }

    /**
     * Return a list of available functions
     *
     * @throws \BadMethodCallException
     * @return array
     */
    public function getFunctions()
    {
        if (null === $this->wsdl)
        {
            throw new \BadMethodCallException('"getFunctions()" method is available only in WSDL mode.');
        }
        return $this->getSoapClient()->__getFunctions();
    }

    /**
     * Return a list of SOAP types
     *
     * @throws \BadMethodCallException
     * @return array
     */
    public function getTypes()
    {
        if (null === $this->wsdl)
        {
            throw new \BadMethodCallException('"getTypes()" method is available only in WSDL mode.');
        }
        return $this->getSoapClient()->__getTypes();
    }

    /**
     * setCookie()
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function setCookie($name, $value= null)
    {
        $this->getSoapClient()->__setCookie($name, $value);
    }

    /**
     * Returns the soap client.
     *
     * @return SoapClientCommon
     */
    protected function getSoapClient()
    {
        if (null === $this->soapClient)
        {
            $this->soapClient = $this->initSoapClientObject();
        }
        return $this->soapClient;
    }

    /**
     * Initialize SOAP Client object
     *
     *
     * @throws ClientException\RedundantOptionException
     * @throws ClientException\MissingOptionException
     * @return SoapClientCommon
     */
    protected function initSoapClientObject()
    {
        $wsdl = $this->getWsdl();

		// INJECT DEFAULT OPTIONS
        $this->options = array_merge(
            array(
                 'encoding'        => self::DEFAULT_OPTION_ENCODING,
                 'soap_version'    => SOAP_1_2,
                 'features'        => SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE,
                 'trace'           => self::DEFAULT_OPTION_TRACE,
                 'exceptions'      => self::DEFAULT_OPTION_EXCEPTIONS,
                 'cache_wsdl'      => WSDL_CACHE_MEMORY,
                 'useragent'       => self::DEFAULT_OPTION_USER_AGENT,
                 'keep_alive'      => true
            ),

            $this->getOptions(),

            array(
                 'classmap' => $this->classMap,
                 'typemap' => $this->typeMap
            )
        );

        if (null === $wsdl)
        {
            if (null === $this->getOption('location'))
            {
                throw new ClientException\MissingOptionException('"location" option is required in non-WSDL mode.');
            }
            if (null === $this->getOption('uri'))
            {
                throw new ClientException\MissingOptionException('"uri" option is required in non-WSDL mode.');
            }
        }
        else
        {
            if (null !== $this->getOption('use'))
            {
                throw new ClientException\RedundantOptionException('"use" option only works in non-WSDL mode.');
            }
            if (null !== $this->getOption('style'))
            {
                throw new ClientException\RedundantOptionException('"style" option only works in non-WSDL mode.');
            }
        }

        // var_dump(isset($this->options['location']) ? $this->options['location'] : 'No location set');

        return new SoapClientCommon(
            array($this, 'doRequestCallback'),
            $wsdl,
            $this->options
        );
    }

    /**
     * Performs a soap call to the given $name
     * action and the $args arguments list.
     *
     * $args may be an array with named key-value-
     * pairs or a popo (plain old php object).
     *
     * @abstract
     *
     * @param $name
     * @param mixed $arguments
     *
     * @param Hydrator\HydratorInterface|null $hydrator
     * @throws SoapFault|TracedSoapFault
     * @return mixed
     */
    public function soapCall($name, $arguments, Hydrator\HydratorInterface $hydrator = null)
    {
        $soapClient = $this->getSoapClient();

        $headers = array_merge($this->permanentSoapInputHeaders, $this->soapInputHeaders);

        $this->soapInputHeaders = array();

        $result = null;

        try
        {
            $result = $soapClient->__soapCall(
                $name,
                $this->preProcessArguments($arguments),
                null, // Options are already set to the SOAP client object
                count($headers) > 0 ? $headers : null,
                $this->soapOutputHeaders
            );

            if($result instanceof GenericSoapFault)
            {
                return new SoapFault($result);
            }
        }
        catch(GenericSoapFault $e)
        {
            $ex = null;

            if($this->getOption('trace'))
            {
                $ex = new TracedSoapFault(
                    $this->getLastRequest(),
                    $this->getLastRequestHeaders(),
                    $this->getLastResponse(),
                    $this->getLastResponseHeaders(),
                    $e
                );
            }
            else
            {
                $ex = new SoapFault($e);
            }
            if(true === $this->getOption('exceptions'))
            {
                throw $ex;
            }
            return $ex;
        }

        return $this->postProcessResult($this->preProcessResult($result), $hydrator);
    }

    /**
     * "Magic" method for performing soap calls.
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->soapCall($name, $arguments);
    }

    /**
     * Adds classes to the connection's soap classmap.
     *
     * @param string $complexType
     * @param string $class
     * @throws ClientException\MappingException
     * @return void
     */
    public function registerClass($complexType, $class)
    {
        $this->soapClient = null;

        if( ! class_exists($class))
        {
            if('\\' === $class[0])
            {
                $class = substr($class, 1);
            }

            if(! class_exists($class))
            {
                throw new ClientException\MappingException(sprintf('Complex type mapping class "%s" does not exist. (Tried to map soap class "%s".)', $class, $this->getOption('uri') . '.' . $complexType));
            }
        }

        if( ! in_array(self::CLASS_MAP_INTERFACE, class_implements($class)))
        {
            throw new ClientException\MappingException(sprintf('Complex type class "%s" must implement interface "%s"! (Tried to map soap class "%s".)', $class, self::CLASS_MAP_INTERFACE, $this->getOption('uri') . '.' . $complexType));
        }

        $this->classMap[$complexType] = $class;
    }

    /**
     * Adds types to the connection's soap typemap.
     *
     *
     * @param string $typename
     * @param string $classname
     * @param string $namespace
     *
     * @throws \Codemitte\Soap\Client\Exception\MappingException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function registerType($typename, $classname, $namespace = null)
    {
        $this->soapClient = null;

        if( ! class_exists($classname))
        {
            throw new ClientException\MappingException(sprintf('Simple type mapping class "%s" does not exist! (Tried to map %s)', $classname, $namespace . '.'. $typename));
        }

        if( ! in_array(self::TYPE_MAP_INTERFACE, class_implements($classname)))
        {
            throw new ClientException\MappingException(sprintf('Stimple type mapping class "%s" must implement interface! (Tried to map %s)', $classname, $namespace . '.' . $typename));
        }

        if(null === $namespace)
        {
            $namespace = $classname::getURI();

            if(null === $namespace)
            {
                $namespace = $this->getOption('uri');

                if(null === $namespace)
                {
                    throw new \InvalidArgumentException('A namespace must be provided, non given and no global URI defined.');
                }
            }
        }

        // Avoid duplicates
        $this->typeMap = array_merge($this->typeMap, array(
            array(
                'type_ns' => $namespace,
                'type_name' => $typename,
                'from_xml' => array($classname, 'fromXml'),
                'to_xml' => array($classname, 'toXml')
            )
        ));
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
        $retVal = array(
            'decorator' => $this->decorator,
            'hydrator' => $this->hydrator,
            'options' => $this->options,
            'classMap' => $this->classMap,
            'typeMap' => $this->typeMap,
            'wsdl' => $this->wsdl,
            'permanentSoapInputHeaders' => $this->permanentSoapInputHeaders,
            'soapOutputHeaders' => $this->soapOutputHeaders
        );

        return serialize($retVal);
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

        $this->decorator                        = $data['decorator'];
        $this->hydrator                         = $data['hydrator'];
        $this->options                          = $data['options'];
        $this->classMap                         = $data['classMap'];
        $this->typeMap                          = $data['typeMap'];
        $this->wsdl                             = $data['wsdl'];
        $this->permanentSoapInputHeaders        = $data['permanentSoapInputHeaders'];
        $this->soapOutputHeaders                = $data['soapOutputHeaders'];

        // POUR LE NEXT REQUEST
        if(count($this->permanentSoapInputHeaders) > 0)
        {
            $this->setSoapInputHeaders($this->permanentSoapInputHeaders, true);
        }
    }

    /**
     * preProcessArguments()
     *
     * @param array $arguments
     * @param \Codemitte\Soap\Client\Decorator\DecoratorInterface $decorator
     * @return array
     */
    public function preProcessArguments(array $arguments, DecoratorInterface $decorator = null)
    {
        if(null === $decorator)
        {
            $decorator = $this->decorator;
        }
        return $decorator->decorate($arguments);
    }

    /**
     * preProcessResult()
     *
     * @param $result
     * @return mixed
     */
    public function preProcessResult($result)
    {
        return $result;
    }

    /**
     * postProcessResult()
     *
     * @param mixed $result
     * @param Hydrator\HydratorInterface $hydrator
     * @return mixed
     */
    public function postProcessResult($result, Hydrator\HydratorInterface $hydrator = null)
    {
        if(null == $hydrator)
        {
            $hydrator = $this->hydrator;
        }

        $retVal = $hydrator->hydrate($result);

        return $retVal;
    }

    /**
     * doRequestCallback()
     *
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
        $callable = array($client, 'parent::__doRequest');

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

    /**
     * normalizeOptions()
     *
     * @throws ClientException\UnknownOptionException
     * @param array $options
     * @return array $normalizedKeys
     */
    protected function normalizeOptions(array $options)
    {
        $retVal = array();

        foreach($options AS $key => $value)
        {
            $retVal[$this->normalizeOption($key)] = $value;
        }

        return $retVal;
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
        switch ($key)
        {
            case 'actor':
                return 'actor';
            case 'keepAlive':
            case 'keepalive':
            case 'keep_alive':
                return 'keep_alive';
            case 'trace':
                return 'trace';
            case 'connection_timeout':
            case 'connectionTimeout':
            case 'timeout':
                return 'connection_timeout';
            case 'encoding':
                return 'encoding';
            case 'soapVersion':
            case 'soap_version':
            case 'soapversion':
                return 'soap_version';
            case 'uri':
                return 'uri';
            case 'location':
                return 'location';
            case 'style':
                return 'style';
            case 'use':
                return 'use';
            case 'login':
                return 'login';
            case 'password':
                return 'password';
            case 'proxy_host':
            case 'proxyHost':
                return 'proxy_host';
            case 'proxy_port':
            case 'proxyPort':
                return 'proxy_port';
            case 'proxy_login':
            case 'proxyLogin':
                return 'proxy_login';
            case 'proxyPassword':
            case 'proxy_password':
                return 'proxy_password';
            case 'local_cert':
            case 'localCert':
            case 'localcert':
                return 'local_cert';
                break;
            case 'passphrase':
            case 'passPhrase':
            case 'pass_phrase':
                return 'passphrase';
            case 'compression':
                return 'compression';
            case 'stream_context':
            case 'streamContext':
            case 'streamcontext':
                return 'stream_context';
            case 'features':
                return 'features';
            case 'cacheWsdl':
            case 'cache_wsdl':
                return 'cache_wsdl';
            case 'useragent':
            case 'userAgent':
            case 'user_agent':
                return 'user_agent';
            case 'exceptions':
                return 'exceptions';

            // Not used now
            // case 'connectionTimeout':
            // case 'connection_timeout':
            //     return 'connection_timeout';

            default:
                throw new ClientException\UnknownOptionException(sprintf('Unknown SOAP client option "%s"', $key));
        }
    }

    /**
     * Adds a bunch of classes mapped by their filenames
     * to the classmap option.
     *
     * @todo implement me!
     * @param $dirname
     * @throws \RuntimeException
     * @throws ClientException\ClassMapRegistrationException
     * @return void
     */
    public function registerClassDir($dirname)
    {
        throw new \RuntimeException('Not implemented!');

        if( ! is_dir($dirname))
        {
            throw new ClientException\ClassMapRegistrationException(sprintf('Directory "%" could not be found.'));
        }

        if( ! is_readable($dirname))
        {
            throw new ClientException\ClassMapRegistrationException(sprintf('Directory "%" cannot be read.'));
        }

        foreach(new \RecursiveIteratorIterator(\RecursiveDirectoryIterator($dirname)) AS $file)
        {
            if( ! $file->isFile() && ! $file->isDot())
            {
                // DO STUFF
            }
        }
    }

    /**
     * Returns the connection's default hydrator.
     *
     * @return Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
}
