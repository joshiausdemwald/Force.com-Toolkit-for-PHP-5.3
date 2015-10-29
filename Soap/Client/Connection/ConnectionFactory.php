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

use Psr\Log\LoggerInterface;

use Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection;
use Codemitte\ForceToolkit\Soap\Mapping\Base\login;
use Codemitte\ForceToolkit\Soap\Client\ClientDisabledException;
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
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var int
     */
    private $loginAttemptLimits;

    /**
     * @var string
     */
    private $storage_namespace;

    /**
     * @var string
     */
    private $notificationEmailFrom;

    /**
     * @var string
     */
    private $notificationEmailTo;

    /**
     * @var string
     */
    private $notificationEmailSubject;

    /**
     * @var string
     */
    private $notificationEmailBody;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param StorageInterface $connectionStorage
     * @param \Swift_Mailer $mailer
     * @param $loginAttemptLimits
     * @param $storage_namespace
     * @param $notificationEmailFrom
     * @param $notificationEmailTo
     * @param $notificationEmailSubject
     * @param $notificationEmailBody
     * @param $connectionTTL
     * @param array $defaultUserConfig
     * @param array $localeUsersConfig
     * @param $wsdlLocation
     * @param $soapServiceLocation
     * @param bool $debug
     */
    public function __construct(
        StorageInterface $connectionStorage,
        \Swift_Mailer $mailer,
        $loginAttemptLimits,
        $storage_namespace,
        $notificationEmailFrom,
        $notificationEmailTo,
        $notificationEmailSubject,
        $notificationEmailBody,
        $connectionTTL,
        array $defaultUserConfig,
        array $localeUsersConfig,
        $wsdlLocation,
        $soapServiceLocation,
        $debug = false
    ) {
        $this->connectionStorage     = $connectionStorage;

        $this->mailer                = $mailer;

        $this->loginAttemptLimits         = $loginAttemptLimits;

        $this->storage_namespace          = $storage_namespace;

        $this->notification_email_from    = $notificationEmailFrom;

        $this->notification_email_to      = $notificationEmailTo;

        $this->notification_email_subject = $notificationEmailSubject;

        $this->notification_email_body    = $notificationEmailBody;

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
     * After a failed login, the next login attempt is delayed
     * And an email wiil be sent to alert about these issues
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

            $loginFirstFail = time();
            $loginAttempt = 0;
            $loginFirstFailCacheKey = $this->storage_namespace . '_loginFirstFail';
            $loginAttemptCacheKey   = $this->storage_namespace . '_loginAttempt';
            $adminNotifiedCacheKey = $this->storage_namespace . '_adminNotified';

            if (apc_exists($loginFirstFailCacheKey)) {
                $loginFirstFail =  apc_fetch($loginFirstFailCacheKey);
            }
            else {
                apc_store($loginFirstFailCacheKey, $loginFirstFail);
            }
            if (apc_exists($loginAttemptCacheKey)) {
                $loginAttempt = apc_fetch($loginAttemptCacheKey);
            }

            $delay = 7.5 * pow($loginAttempt, 2);

            if ($loginAttempt >= $this->loginAttemptLimits) {
                //send email for notification
                if (!apc_exists($adminNotifiedCacheKey)) {
                    try {
                        // Send the message
                        $message = \Swift_Message::newInstance()
                            ->setSubject(sprintf($this->notification_email_subject,$loginAttempt))
                            ->setFrom($this->notification_email_from)
                            ->setTo(explode(',',$this->notification_email_to))
                            ->setBody($this->notification_email_body);
                        $this->mailer->send($message);

                    } catch (\Exception $e) {
                        $this->logger->error("Could not send email to {$this->notification_email_to} for invalid api user credentials: " . $e->getMessage() );
                    }

                    apc_store($adminNotifiedCacheKey, true, 60 * 60); //only one email per hour
                }

                throw new ClientDisabledException('Login disabled');
            }

            if (time() - $loginFirstFail < $delay ) {
                throw new ClientDisabledException('Login disabled');
            }

            try {
                $connection->login();
                apc_store($loginAttemptCacheKey, 0);
                apc_delete($loginFirstFailCacheKey);
                apc_delete($adminNotifiedCacheKey);
            }
            catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->logger->info("delaying login {$delay} seconds");
                $loginAttempt++;
                apc_store($loginAttemptCacheKey, $loginAttempt);
                throw new ClientDisabledException($e->getMessage());
            }

            $this->connectionStorage->set($currentLocale, $connection);

        }

        $connection->setLogger($this->logger);

        return $connection;
    }

    /**
     * Returns the current locale.
     *
     * @return mixed|null
     */
    public function getCurrentLocale()
    {
        return \Locale::getDefault();
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

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
