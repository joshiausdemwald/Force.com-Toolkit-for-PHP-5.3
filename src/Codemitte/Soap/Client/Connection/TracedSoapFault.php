<?php
namespace Codemitte\Soap\Client\Connection;

use \SoapFault AS GenericSoapFault;

/**
 * TracedSoapFault
 */
class TracedSoapFault extends SoapFault
{
    private $lastRequest;

    private $lastRequestHeaders;

    private $lastResponse;

    private $lastResponseHeaders;

    public function __construct($lastRequest, $lastRequestHeaders, $lastResponse, $lastResponseHeaders, GenericSoapFault $fault)
    {
        parent::__construct($fault);

        $this->lastRequest = $lastRequest;

        $this->lastResponse = $lastResponse;

        $this->lastRequestHeaders = $lastRequestHeaders;

        $this->lastResponseHeaders = $lastResponseHeaders;
    }

    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    public function getLastRequestHeaders()
    {
        return $this->lastRequestHeaders;
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }
}
