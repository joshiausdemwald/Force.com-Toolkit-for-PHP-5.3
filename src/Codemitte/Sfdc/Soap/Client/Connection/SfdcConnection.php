<?php
namespace Codemitte\Sfdc\Soap\Client\Connection;

use Codemitte\Sfdc\Soap\Mapping\Base\login;


/**
 * Connection
 */
class SfdcConnection extends AbstractConnection
{
    /**
     * @var \Codemitte\Sfdc\Soap\Mapping\Base\LoginResult
     */
    private $loginResult;

    /**
     * Constructor.
     *
     * @param string $wsdl: The path to the wsdl file.
     * @param string $serviceLocation: The location of the webservice, only used when differs from service definition
     *                                 in wsdl.
     */
    public function __construct($wsdl, $serviceLocation = null)
    {
        parent::__construct($wsdl);

        $options = array(
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE | SOAP_WAIT_ONE_WAY_CALLS,
            'keep_alive' => true,
            'soap_version' => SOAP_1_1
        );

        if(null !== $serviceLocation)
        {
            $options['location'] = $serviceLocation;
        }

        $this->setOptions($options);

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
     * @abstract
     *
     * @param login $credentials
     *
     * @return \Codemitte\Sfdc\Soap\Mapping\Base\loginResponse
     */
    public function login(login $credentials)
    {
        $response = $this->__call('login', array($credentials));

        $this->loginResult = $response->getResult();

        $this->setOption('location', $this->loginResult->getServerUrl());

        return $response;
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
        return array_merge(parent::serialize(), array(
            'loginResult' => $this->getLoginResult()
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
        $data = parent::unserialize($serialized);

        $this->loginResult = $data['loginResult'];
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
}