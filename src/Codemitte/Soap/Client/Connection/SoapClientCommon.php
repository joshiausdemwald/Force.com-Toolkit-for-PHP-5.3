<?php
namespace Codemitte\Soap\Client\Connection;

use \SoapClient;

/**
 * SoapClientCommon.
 * PHP 5.3 compatible rewrite of Zend_Soap_Client_Common
 * (@copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Johannes Heinen <johannes.heinen@code-mitte.de>
 */
class SoapClientCommon extends SoapClient
{
    /**
     * @var string $outputHeaders
     */
    private $outputHeaders;

    /**
     * doRequest() pre-processing method
     *
     * @var callback
     */
    protected $doRequestCallback;

    /**
     * Common Soap Client constructor. doRequestCallback may be any php callable.
     *
     * @param callback $doRequestCallback
     * @param string $wsdl
     * @param array $options
     */
    function __construct($doRequestCallback, $wsdl, array $options = array())
    {
        $this->doRequestCallback = $doRequestCallback;

        parent::__construct($wsdl, $options);
    }

    /**
     * Performs SOAP request over HTTP.
     * Overridden to implement different transport layers, perform additional XML processing or other purpose.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $one_way
     * @return mixed
     */
    function __doRequest($request, $location, $action, $version, $one_way = null)
    {
        $params = array(
            $this, $request, $location, $action, $version
        );

        if (null !== $one_way)
        {
            $params[] = $one_way;
        }
        return call_user_func_array($this->doRequestCallback, $params);
    }

    /**
     * __SoapCall
     *
     * @param $function
     * @param $arguments
     * @param array $options
     * @param null $input_headers
     * @param null $output_headers
     */
    public function __soapCall($function, $arguments, $options = array(), $input_headers = null, &$output_headers = null)
    {
        $retVal = parent::__soapCall($function, $arguments, $options, $input_headers, $output_headers);

        $this->outputHeaders = $output_headers;

        return $retVal;
    }
}
