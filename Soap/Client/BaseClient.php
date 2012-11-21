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

namespace Codemitte\ForceToolkit\Soap\Client;

use \BadMethodCallException;

use Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnectionInterface;
use Codemitte\ForceToolkit\Soap\Header\SessionHeader;
use Codemitte\ForceToolkit\Soap\Header\CallOptions;

/**
 * BaseClient
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 *
 * @abstract
 */
abstract class BaseClient implements BaseClientInterface
{
    /**
     * @var string
     */
    const API_VERSION = '26.0';

    /**
     * @var SfdcConnectionInterface
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param SfdcConnectionInterface $connection
     * @throws SfdcClientException
     */
    public function __construct(SfdcConnectionInterface $connection)
    {
        $this->connection = $connection;

        $this->connection->setURI($this->getUri());

        $this->configure($connection);

        $this->connection->resetSoapInputHeaders();

        if( ! $this->connection->isLoggedIn())
        {
            throw new SfdcClientException('Cannot build client, connection has no login information! Call SfdcConnectionInterface::login() first');
        }

        // ADD PERMANENT SESSION HEADER
        $this->connection->addSoapInputHeader(new SessionHeader(
            $this->getURI(),
            $this->connection->getLoginResult()->getSessionId()
        ), true);

        $this->connection->addSoapInputHeader(new CallOptions(
            $this->getUri(),
            'Force.com Toolkit For PHP 5.3/v1.0.0',
            null
        ), true);
    }

    /**
     * Meant to be overritten by implementations to e.g. register
     * class mappings.
     *
     * @param SfdcConnectionInterface $connection
     */
    protected function configure(SfdcConnectionInterface $connection)
    {

    }

    /**
     * getConnection()
     *
     * @return SfdcConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the API version the client implementation
     * fits to.
     *
     * @return string
     */
    public function getAPIVersion()
    {
        return self::API_VERSION;
    }

    /**
     * Calls any webservice method directly on the connection.
     *
     * @param $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, array $args = array())
    {
        throw new BadMethodCallException(sprintf('Method "%s()" is not yet implemented by client "%s". Use "$client->getConnection()->myMethod()" to settle a raw soap request.', $name, get_class($this)));
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
           'connection' => serialize($this->connection)
       ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     *
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->connection = unserialize($data['connection']);
    }
}
