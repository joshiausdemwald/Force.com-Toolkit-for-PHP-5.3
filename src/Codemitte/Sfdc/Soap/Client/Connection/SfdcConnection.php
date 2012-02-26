<?php
namespace Codemitte\Sfdc\Soap\Client\Connection;

use Codemitte\Soap\Client\Connection\Connection;

use Codemitte\Sfdc\Soap\Mapping\Base\login;


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
 *   -  soap.wsdl_cache_limit	    5	        maximale Anzahl von WSDL-Dateien, die gespeichert werden können
 */
class SfdcConnection extends Connection implements SfdcConnectionInterface
{
    /**
     * Version string to append to the user agent.
     */
    const VERSION = 'v0.0.1Beta';

    /**
     * @var \Codemitte\Sfdc\Soap\Mapping\Base\LoginResult
     */
    private $loginResult;

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
     * @param string $wsdl: The path to the wsdl file.
     * @param string $serviceLocation: The location of the webservice, only used when differs from service definition
     *                                 in wsdl.
     * @param array $options
     */
    public function __construct($wsdl, $serviceLocation = null, array $options = array())
    {
        parent::__construct($wsdl);

        if(null !== $serviceLocation)
        {
            $this->setOption('location', $serviceLocation);
        }

        // FORCE DEFAULT OPTIONS
        $this->setOptions(array_merge($options, array(
            'soap_version' => SOAP_1_1,
            'user_agent' => 'salesforce-toolkit-php53/' . self::VERSION,
            'encoding' => 'utf-8',
            'trace' => true,
            'exceptions' => true,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP
        )));

        $this->registerClass('GetUserInfoResult', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\GetUserInfoResult');
        $this->registerClass('login', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\login');
        $this->registerClass('LoginFault', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\LoginFault');
        $this->registerClass('loginResponse', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\loginResponse');
        $this->registerClass('LoginScopeHeader', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\LoginScopeHeader');
        $this->registerClass('LoginResult', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\LoginResult');
    }

    /**
     * Registers the given username to the specified organisation.
     *
     * @param login $credentials
     *
     * @return \Codemitte\Sfdc\Soap\Mapping\Base\loginResponse
     */
    public function login(login $credentials)
    {
        $response = $this->soapCall('login', array($credentials));

        $this->loginResult = $response->getResult();

        $this->setOption('location', $this->loginResult->getServerUrl());

        return $response;
    }

    /**
     * Returns the login result. Expected to be null unless
     * login() has been called.
     *
     * @return \Codemitte\Sfdc\Soap\Mapping\LoginResult
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
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        return serialize(array(
            'loginResult' => $this->loginResult,
            '__parentData' => parent::serialize()
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

        $this->loginResult = $data['loginResult'];

        parent::unserialize($data['__parentData']);
    }

}