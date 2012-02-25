<?php
namespace Codemitte\Sfdc\Soap\Client\Connection;

use \BadMethodCallException;

use \SoapHeader;
use \SoapFault;

use Codemitte\Sfdc\Soap\Client\Connection\SoapClientCommon;

/**
 * Generic connection.
 *
 * Derived from Zend_Soap_Client (@copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com))
 */
class GenericConnection implements ConnectionInterface
{
    const TYPE_MAP_NAMESPACE = 'Codemitte\\Sfdc\\Soap\\Mapping\\Type';

    const TYPE_MAP_INTERFACE = 'Codemitte\\Sfdc\\Soap\\Mapping\\Type\\TypeInterface';

    const DEFAULT_OPTION_DESERIALIZE_AS_ARRAY = true;

    const DEFAULT_OPTION_ENCODING = 'utf-8';

    const DEFAULT_OPTION_TRACE = true;

    const DEFAULT_OPTION_EXCEPTIONS = true;

    const DEFAULT_OPTION_KEEP_ALIVE = true;

    const DEFAULT_OPTION_CACHE_WSDL = WSDL_CACHE_MEMORY;

    const DEFAULT_OPTION_USER_AGENT = 'Force.com-Toolkit-For-PHP5.3/v0.0.1Beta';

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
     * Constructor.
     *
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl = null, array $options = array())
    {
        $this->wsdl = $wsdl;

        $this->setOption('deserialize_as_array', self::DEFAULT_OPTION_DESERIALIZE_AS_ARRAY);

        $this->setOptions($options);

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
     *
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
     * Add SOAP input header
     *
     * @param SoapHeader $header
     * @param bool $permanent
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
     * @throws MissingOptionException
     *
     * @return string
     */
    public function getLastRequest()
    {
        if(true !== $this->getOption('trace', self::DEFAULT_OPTION_TRACE))
        {
            throw new MissingOptionException('getLastRequest() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastRequest();
    }

    /**
     * Get response XML
     *
     * @throws MissingOptionException
     *
     * @return string
     */
    public function getLastResponse()
    {
        if(true !== $this->getOption('trace', self::DEFAULT_OPTION_TRACE))
        {
            throw new MissingOptionException('getLastResponse() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastResponse();
    }

    /**
     * Retrieve request headers
     *
     * @throws MissingOptionException
     *
     * @return string
     */
    public function getLastRequestHeaders()
    {
        if(true !== $this->getOption('trace'))
        {
            throw new MissingOptionException('getLastRequestHeaders() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastRequestHeaders();
    }

    /**
     * Retrieve response headers (as string)
     *
     * @throws MissingOptionException
     *
     * @return string
     */
    public function getLastResponseHeaders()
    {
        if(true !== $this->getOption('trace'))
        {
            throw new MissingOptionException('getLastResponseHeaders() only works when "trace" option is set to true.');
        }
        return $this->getSoapClient()->__getLastResponseHeaders();
    }

    /**
     * Adds classes to the connection's soap classmap.
     *
     * @param string $type
     * @param string $classname
     */
    public function registerClass($type, $classname)
    {
        $this->soapClient = null;

        $this->classMap[$type] = $classname;
    }

    /**
     * Return a list of available functions
     *
     * @return array
     * @throws Zend_Soap_Client_Exception
     */
    public function getFunctions()
    {
        if (null === $this->wsdl)
        {
            throw new BadMethodCallException('"getFunctions()" method is available only in WSDL mode.');
        }
        return $this->getSoapClient()->__getFunctions();
    }

    /**
     * Return a list of SOAP types
     *
     * @return array
     * @throws Zend_Soap_Client_Exception
     */
    public function getTypes()
    {
        if (null === $this->wsdl)
        {
            throw new BadMethodCallException('"getTypes()" method is available only in WSDL mode.');
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
     * @throws Zend_Soap_Client_Exception
     * @return \Codemitte\Sfdc\Soap\Client\SoapClientCommon
     */
    protected function initSoapClientObject()
    {
        $wsdl = $this->getWsdl();

        // INJECT DEFAULT OPTIONS
        $this->options = array_merge(
            array(
                 'encoding'      => self::DEFAULT_OPTION_ENCODING,
                 'soap_version'  => SOAP_1_2,
                 'features'      => SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE,
                 'trace'         => self::DEFAULT_OPTION_TRACE,
                 'exceptions'    => self::DEFAULT_OPTION_EXCEPTIONS,
                 'keep_alive'    => self::DEFAULT_OPTION_CACHE_WSDL,
                 'cache_wsdl'    => WSDL_CACHE_MEMORY,
                 'useragent'     => self::DEFAULT_OPTION_USER_AGENT
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
                throw new MissingOptionException('"location" option is required in non-WSDL mode.');
            }
            if (null === $this->getOption('uri'))
            {
                throw new MissingParameterException('"uri" option is required in non-WSDL mode.');
            }
        }
        else
        {
            if (null !== $this->getOption('use'))
            {
                throw new RedundantOptionException('"use" option only works in non-WSDL mode.');
            }
            if (null !== $this->getOption('style'))
            {
                throw new RedundantOptionException('"style" option only works in non-WSDL mode.');
            }
        }

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
     * @param $name
     * @param mixed $args
     * @return mixed
     */
    public function soapCall($name, $arguments)
    {
        $soapClient = $this->getSoapClient();

        // MERGES BOTH PERMANENT AND NON-PERMANENT HEADERS
        $soapHeaders = $this->getSoapInputHeaders();

        $result = $soapClient->__soapCall(
            $name,
            $this->preProcessArguments($arguments),
            null, // Options are already set to the SOAP client object
            count($soapHeaders) > 0 ? $soapHeaders : null,
            $this->soapOutputHeaders
        );

        // Reset non-permanent input headers
        $this->soapInputHeaders = array();

        return $this->postProcessResult($this->preProcessResult($result));
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
     * Adds types to the connection's soap typemap.
     *
     * @throws RuntimeException
     * @param string $namespace
     * @param string $typename
     * @param string $class
     */
    public function registerType($typename, $class, $namespace = null)
    {
        $this->soapClient = null;

        $classname = self::TYPE_MAP_NAMESPACE . '\\' . $class;

        if(null === $namespace)
        {
            $namespace = $this->getOption('uri');

            if(null === $namespace)
            {
                throw new InvalidArgumentException('A namespace must be provided, non given and no global URI defined.');
            }
        }

        if( ! class_exists($classname))
        {
            throw new RuntimeException(sprintf('Class "%s" does not exist!', $class));
        }

        if( ! in_array(self::TYPE_MAP_INTERFACE, class_implements($classname)))
        {
            throw new RuntimeException(sprintf('Type map Class "%s" must implement interface !', $class));
        }

        // Avoid duplicates
        $this->typeMap[$namespace.$typename] = array (
            'type_ns' => $namespace,
            'type_name' => $typename,
            'from_xml' => array($classname, 'fromXml'),
            'to_xml' => array($classname, 'toXml')
        );
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
        $this->soapClient = null;

        $this->soapInputHeaders = array();

        $retVal = array();

        foreach($this AS $key => $value)
        {
            $retVal[$key] = $value;
        }
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

        foreach($data AS $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * preProcessArguments()
     *
     * @internal
     *
     * @param array $arguments
     *
     * @return array
     */
    public function preProcessArguments(array $arguments)
    {
        return $arguments;
    }

    /**
     * preProcessResult()
     *
     * @internal
     *
     * @param $result
     *
     * @return mixed
     */
    public function preProcessResult($result)
    {
        return $result;
    }

    /**
     * @internal
     *
     * @param $result
     *
     * @return mixed
     */
    public function postProcessResult($result)
    {
        if(
            is_object($result) &&
            $result instanceof \stdClass &&
            $this->getOption('deserialize_as_array', self::DEFAULT_OPTION_DESERIALIZE_AS_ARRAY))
        {
            return (array)$result;
        }
        return $result;
    }

    /**
     * doRequestCallback()
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
     * @throws UnknownOptionException
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
     * @throws UnknownOptionException
     * @param string $key
     * @return string $normalizedKey
     */
    protected function normalizeOption($key)
    {
        switch ($key)
        {
            case 'deserializeAsArray':
            case 'deserialize_as_array':
                return 'deserialize_as_array';
            case 'trace':
                return 'trace';
                break;
            case 'keepAlive':
            case 'keepalive':
            case 'keep_alive':
                return 'keep_alive';
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
                throw new UnknownOptionException(sprintf('Unknown SOAP client option "%s"', $key));
        }
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
}
