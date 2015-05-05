<?php
namespace Codemitte\ForceToolkit\Soap\Client\Connection\Storage;

use Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnectionInterface;

class APCStorage implements StorageInterface
{
    /**
     * @var String
     */
    private $namespace;

    /**
     * @param $namespace
     */
    public function __construct($namespace) {

        $this->namespace = $namespace;
    }

    /**
     * @param string $locale
     * @return bool|\string[]
     */
    public function has($locale)
    {
       return apc_exists($this->genKey($locale));
    }

    /**
     * @param string $locale
     * @return SfdcConnectionInterface
     */
    public function get($locale)
    {
        $result = null;

        $retVal = unserialize(apc_fetch($this->genKey($locale, $result)));

        if(false === $result)
        {
            return null;
        }
        return $retVal;
    }

    /**
     * @param string $locale
     * @param SfdcConnectionInterface $connection
     * @return mixed
     */
    public function set($locale, SfdcConnectionInterface $connection)
    {
        apc_store($this->genKey($locale), serialize($connection));
    }

    /**
     * @param $locale
     * @return string
     */
    private function genKey($locale)
    {
        $key = '__sfdc_client_';

        if($namespace) {
            $key .= $namespace . '_';
        }

        return base64_encode($key . $locale);
    }

    /**
     * @param string $locale
     * @return void
     */
    public function remove($locale)
    {
        apc_delete($this->genKey($locale));
    }
}
