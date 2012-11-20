<?php
namespace Codemitte\ForceToolkit\Soap\Mapping\Base;

use Codemitte\Soap\Mapping\ClassInterface;

class CallOptions implements ClassInterface
{
    /**
     * @var string $client
     *
     * @access private
     */
    private $client;

    /**
     * @var string
     */
    private $defaultNamespace;

    /**
     * @param string $client
     * @param $defaultNamespace
     *
     * @access public
     */
    public function __construct($client, $defaultNamespace)
    {
        $this->client = $client;

        $this->defaultNamespace = $defaultNamespace;
    }

    /**
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getDefaultNamespace()
    {
        return $this->defaultNamespace;
    }
}
