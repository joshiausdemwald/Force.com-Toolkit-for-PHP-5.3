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

namespace Codemitte\Sfdc\Soap\Client;

/**
 * @todo: Guess service location endpoint out ouf the provided soap definition wsdl
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
abstract class AbstractCustomWsClient extends BaseClient implements CustomWsClientInterface
{
    /**
     * @var API $epsClient;
     */
    private $apiClient;

    /**
     * Constructor.
     *
     * @param \Codemitte\Sfdc\EPS\Soap\Client\API $apiClient
     */
    public function __construct($wsdl, API $apiClient)
    {
        $this->apiClient = $apiClient;

        // HOLDS THE SESSION TOKEN
        /* @var $clientConnection \Codemitte\Sfdc\Soap\Client\Connection\SfdcConnection */
        $clientConnection = $this->apiClient->getConnection();

        /* LOL --- UGLY AS HELL -, -
        // Update: Copy the URL returned by login to the endpoint for our web service
            int idx1 = lr.serverUrl.IndexOf(@"/services/");
            int idx2 = myBinding.Url.IndexOf(@"/services/");
            if (idx1 == 0 || idx2 == 0)
            {
                MessageBox.Show("Invalid URL strings in bindings");
                return;
            }
            myBinding.Url = lr.serverUrl.Substring(0, idx1) + myBinding.Url.Substring(idx2);
        */

        /* @var $myConnection \Codemitte\Sfdc\Soap\Client\Connection\SfdcConnection */
        $myConnection = new \Codemitte\Sfdc\Soap\Client\Connection\SfdcConnection(
            $clientConnection->getCredentials(),
            $wsdl,
            $this->locateEndpoint($clientConnection->getLocation(), $this->getSuffix())
        );
        $myConnection->setLoginResult($clientConnection->getLoginResult());

        // (RE-)SETS SESSION HEADER
        parent::__construct($myConnection);
    }

    /**
     * @return \Codemitte\Sfdc\EPS\Soap\Client\API|API
     */
    public function getApiCLient()
    {
        return $this->apiClient;
    }

    /**
     * @param $apiEndpoint
     * @param $customWsdlEndpoint
     * @return string
     */
    protected function locateEndpoint($apiClientEndpoint, $customWsSuffix)
    {
        return strstr($apiClientEndpoint, 'services/Soap', true) . 'services/Soap/' . $customWsSuffix;
    }
}