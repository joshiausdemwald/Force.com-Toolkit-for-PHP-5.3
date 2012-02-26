<?php
namespace Codemitte\Sfdc\Soap\Client;

use \SoapHeader;
use \Serializable;
use \BadMethodCallException;

use Codemitte\Sfdc\Soap\Client\Connection\SfdcConnectionInterface;

use Codemitte\Sfdc\Soap\Mapping\Base\SessionHeader;
/**
 * BaseClient
 *
 * @abstract
 */
abstract class BaseClient implements SoapClientInterface, Serializable
{
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

        // ADD PERMANENT SESSION HEADER
        $this->connection->addSoapInputHeader(
            new \SoapHeader($this->getUri(), 'SessionHeader', new SessionHeader(
                    $this->connection->getLoginResult()->getSessionId()
                ),
                true
            ),
            true
        );

        $this->connection->registerClass('SessionHeader', '\\Codemitte\\Sfdc\\Soap\\Mapping\\Base\\SessionHeader');

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
