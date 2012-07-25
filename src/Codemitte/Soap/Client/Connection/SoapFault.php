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

namespace Codemitte\Soap\Client\Connection;

use \SoapFault AS GenericSoapFault;

/**
 * SoapFault
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
 */
class SoapFault extends GenericSoapFault
{
    /**
     * @param GenericSoapFault $fault
     */
    public function __construct(GenericSoapFault $fault)
    {

        parent::__construct(
            isset($fault->faultcode) ? $fault->faultcode : null,
            isset($fault->faultstring) ? $fault->faultstring : null,
            isset($fault->faultactor) ? $fault->faultactor : null,
            isset($fault->detail) ? $fault->detail : null,
            isset($fault->_name) ? $fault->_name : null,
            isset($fault->headerfault) ? $fault->headerfault : null
        );
    }

    public function getFaultactor()
    {
        return isset($this->faultactor) ? $this->faultactor : null;
    }

    public function getFaultcode()
    {
        return isset($this->faultcode) ? $this->faultcode: null;
    }

    public function getFaultdetail()
    {
        return isset($this->detail) ? $this->detail : null;
    }

    public function getFaultname()
    {
        return isset($this->_name) ? $this->_name : null;
    }

    public function getFaultstring()
    {
        return isset($this->faultstring) ? $this->faultstring : null;
    }

    public function getHeaderfault()
    {
        return isset($this->headerfault) ? $this->headerfault : null;
    }
}