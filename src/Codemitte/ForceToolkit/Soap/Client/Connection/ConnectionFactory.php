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

use Symfony\Component\Locale\Locale;

use Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection;
use Codemitte\ForceToolkit\Soap\Mapping\Base\login;
use Codemitte\ForceToolkit\Soap\Client\Connection\Storage\StorageInterface;

/**
 * ConnectionFactory
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage EPS
 */
final class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @var Storage\StorageInterface
     */
    private $connectionStorage;

    /**
     * @var array
     */
    private $defaultUserConfig;

    /**
     * @var array
     */
    private $localeUsersConfig;

    /**
     * @var string
     */
    private $wsdlLocation;

    /**
     * @var string
     */
    private $soapServiceLocation;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var int
     */
    private $connectionTTL;

    /**
     * Constructor.
     *
     * @param StorageInterface $connectionStorage
     * @param $connectionTTL
     * @param array $defaultUserConfig
     * @param array $localeUsersConfig
     * @param $wsdlLocation
     * @param $soapServiceLocation
     * @param bool $debug
     */
    public function __construct(
        StorageInterface $connectionStorage,
        $connectionTTL,
        array $defaultUserConfig,
        array $localeUsersConfig,
        $wsdlLocation,
        $soapServiceLocation,
        $debug = false
    ) {
        $this->connectionStorage     = $connectionStorage;

        $this->connectionTTL         = $connectionTTL;

        $this->defaultUserConfig     = $defaultUserConfig;

        $this->localeUsersConfig     = $localeUsersConfig;

        $this->wsdlLocation          = $wsdlLocation;

        $this->soapServiceLocation   = $soapServiceLocation;

        $this->debug                 = $debug;
    }

    /**
     * Returns or creates and returns a new
     * client instance based on the current
     * locale.
     *
     * @throws \InvalidArgumentException
     * @return SfdcConnectionInterface
     */
    public function getInstance()
    {
        $currentLocale = $this->getCurrentLocale();

        $userConfig = $this->getUserConfig($currentLocale);

        if(null === $userConfig)
        {
            throw new \InvalidArgumentException(sprintf('Unsupported sfdc client locale "%s".', $currentLocale === null ? 'null' : $currentLocale));
        }

        if( ! ($connection = $this->connectionStorage->get($currentLocale)) || $this->needsRelogin($connection))
        {
            $credentials = new login($userConfig['username'], $userConfig['password']);

            $connection = new SfdcConnection($credentials, $this->wsdlLocation, $this->soapServiceLocation, array(), $this->debug);

            $connection->login();

            $this->connectionStorage->set($currentLocale, $connection);
        }
        return $connection;
    }

    /**
     * Returns the current locale.
     *
     * @return mixed|null
     */
    public function getCurrentLocale()
    {
        return Locale::getDefault();
    }

    /**
     * @param string|null $locale
     * @return array|null
     */
    public function getUserConfig($locale = null)
    {
        if(null === $locale)
        {
            return $this->defaultUserConfig;
        }
        elseif(array_key_exists($locale, $this->localeUsersConfig))
        {
            return $this->localeUsersConfig[$locale];
        }
        return $this->defaultUserConfig;
    }

    /**
     * Returns true if the given connection has timed out.
     *
     * @param SfdcConnectionInterface $con
     *
     * @return bool
     */
    private function needsRelogin(SfdcConnectionInterface $con)
    {
        return ! $con->isLoggedIn() || (time() - $con->getLastLoginTime()) > $this->connectionTTL;
    }
}
