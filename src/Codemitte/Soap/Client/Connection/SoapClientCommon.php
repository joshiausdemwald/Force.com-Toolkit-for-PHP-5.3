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

use \SoapClient;
use \SoapFault;

/**
 * SoapClientCommon.
 * PHP 5.3 compatible rewrite of Zend_Soap_Client_Common
 * (@copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
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
     * @param callable $doRequestCallback
     * @param string $wsdl
     * @param array $options
     */
    function __construct($doRequestCallback, $wsdl, array $options = array())
    {
        $this->doRequestCallback = $doRequestCallback;

        parent::__construct($wsdl, $options);
    }

    /**
     * __SoapCall
     *
     * @param $function
     * @param $arguments
     * @param array $options
     * @param null $input_headers
     * @param null $output_headers
     *
     * @return mixed
     */
    public function __soapCall($function, $arguments, $options = null, $input_headers = null, & $output_headers = null)
    {
        $retVal = parent::__soapCall($function, $arguments, $options, $input_headers, $output_headers);

        $this->outputHeaders = $output_headers;

        return $retVal;
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
        $retVal = call_user_func_array($this->doRequestCallback, $params);

        return $retVal;
    }

    /**
     * __doRequestFallback
     *
     * @param $request
     * @param $location
     * @param $action
     * @param $version
     * @param int $one_way
     * @return mixed
     * @throws SoapFault
     */
    public function __doRequestFallback($request, $location, $action, $version, $one_way = null)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $location);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
          'Content-type: text/xml;charset="utf-8"',
          'Accept: text/xml',
          'Cache-Control: no-cache',
          'Pragma: no-cache',
          'SOAPAction: ' . '"' . $action . '"',
          'Content-length: ' . mb_strlen($request))
        );

        curl_setopt($handle, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($handle, CURLOPT_POSTFIELDS,        $request);
        curl_setopt($handle, CURLOPT_SSLVERSION,        3);
        curl_setopt($handle, CURLOPT_PORT,              443);
        curl_setopt($handle, CURLOPT_POST,              true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST,    false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER,    false);

        $response = curl_exec($handle);

        if(empty($response))
        {
            throw new SoapFault('CURL error: ' . curl_error($handle), curl_errno($handle));
        }

        curl_close($handle);

        if(null === $one_way  || ! $one_way)
        {
            return $response;
        }
    }
}
