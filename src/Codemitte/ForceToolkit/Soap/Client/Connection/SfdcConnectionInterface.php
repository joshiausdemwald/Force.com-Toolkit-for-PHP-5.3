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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Codemitte\Soap\Client\Connection\ConnectionInterface;
use Codemitte\ForceToolkit\Soap\Mapping\Base\login;
use Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult;

/**
 * SfdcConnectionInterface
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
interface SfdcConnectionInterface extends ConnectionInterface
{
    /**
     * Registers the given username to the specified organisation.
     *
     * @abstract
     *
     * @internal param \Codemitte\ForceToolkit\Soap\Mapping\Base\login $credentials
     * @return \Codemitte\ForceToolkit\Soap\Mapping\Base\loginResponse
     */
    public function login();

    /**
     * Logs the current user out.
     *
     * @abstract
     *
     * @return void
     */
    public function logout();

    /**
     * Returns the login result. Expected to be null unless
     * login() has been called.
     *
     * @return \Codemitte\ForceToolkit\Soap\Mapping\LoginResult
     */
    public function getLoginResult();

    /**
     * To inject the session id of another connection
     * into this one.
     *
     * @abstract
     * @param \Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult $loginResult
     * @return mixed
     */
    public function setLoginResult(LoginResult $loginResult);

    /**
     * Returns true if a login result exists.
     *
     * @return bool
     */
    public function isLoggedIn();

    /**
     * @abstract
     * @return login
     */
    public function getCredentials();

    /**
     * @abstract
     * @return bool
     */
    public function getDebug();

    /**
     * @abstract
     * @return int
     */
    public function getLastLoginTime();
}