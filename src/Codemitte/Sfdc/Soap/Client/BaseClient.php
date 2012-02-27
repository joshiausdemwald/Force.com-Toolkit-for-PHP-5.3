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

use \SoapHeader;
use \Serializable;
use \BadMethodCallException;

use Codemitte\Sfdc\Soap\Client\Connection\SfdcConnectionInterface;

use Codemitte\Sfdc\Soap\Header\SessionHeader;
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
abstract class BaseClient implements ClientInterface
{
    /**
     * @var string
     */
    const API_VERSION = '23.0';

    /**
     * @var SfdcConnectionInterface
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param SfdcConnectionInterface $connection
     */
    public function __construct(SfdcConnectionInterface $connection)
    {
        $this->connection = $connection;

        $this->connection->setOption('uri', $this->getUri());

        $connection->registerClass('SessionHeader', 'Codemitte\\Sfdc\\Soap\\Mapping\\Base\\SessionHeader');

        // ADD PERMANENT SESSION HEADER
        $this->connection->addSoapInputHeader(new SessionHeader(
            $this->getUri(),
            $this->connection->getLoginResult()->getSessionId()
        ), true);

        $this->configure($connection);
    }

    /**
     * Meant to be overritten by implementations to e.g. register
     * class mappings.
     *
     * @param SfdcConnectionInterface $connection
     */
    protected function configure(SfdcConnectionInterface $connection) {

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
       return serialize(array('connection' => $this->connection));
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

        $this->connection = $data['connection'];
    }
}
