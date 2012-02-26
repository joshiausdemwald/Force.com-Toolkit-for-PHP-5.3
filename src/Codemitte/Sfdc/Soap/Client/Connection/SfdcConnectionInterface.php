<?php
namespace Codemitte\Sfdc\Soap\Client\Connection;

use Codemitte\Soap\Client\Connection\ConnectionInterface;
use Codemitte\Sfdc\Soap\Mapping\Base\login;

/**
 * SfdcConnectionInterface
 */
interface SfdcConnectionInterface extends ConnectionInterface
{
    /**
     * Registers the given username to the specified organisation.
     *
     * @abstract
     *
     * @param login $credentials
     *
     * @return \Codemitte\Sfdc\Soap\Mapping\Base\loginResponse
     */
    public function login(login $credentials);

    /**
     * Returns the login result. Expected to be null unless
     * login() has been called.
     *
     * @return \Codemitte\Sfdc\Soap\Mapping\LoginResult
     */
    public function getLoginResult();

    /**
     * Returns true if a login result exists.
     *
     * @return bool
     */
    public function isLoggedIn();
}
