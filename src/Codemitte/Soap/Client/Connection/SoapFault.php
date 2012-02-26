<?php
namespace Codemitte\Soap\Client\Connection;

use \SoapFault AS GenericSoapFault;

/**
 * SoapFault
 */
class SoapFault extends GenericSoapFault
{
    private $faultcode;

    private $faultstring;

    private $faultactor;

    private $faultdetail;

    private $faultname;

    private $headerfault;

    /**
     * @param \GenericSoapFault $fault
     */
    public function __construct(GenericSoapFault $fault)
    {
        $this->faultcode = $fault->faultcode;
        $this->faultstring = $fault->faultstring;
        $this->faultactor = @$fault->faultactor;
        $this->faultdetail  = @$fault->detail;
        $this->faultname = @$fault->_name;
        $this->headerfault = @$fault->headerfault;

        parent::__construct($this->faultcode, $this->faultstring, $this->faultactor, $this->faultdetail, $this->faultname, $this->headerfault);
    }

    public function getFaultactor()
    {
        return $this->faultactor;
    }

    public function getFaultcode()
    {
        return $this->faultcode;
    }

    public function getFaultdetail()
    {
        return $this->faultdetail;
    }

    public function getFaultname()
    {
        return $this->faultname;
    }

    public function getFaultstring()
    {
        return $this->faultstring;
    }

    public function getHeaderfault()
    {
        return $this->headerfault;
    }

}
