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

namespace Codemitte\ForceToolkit\Soap\Header;

use \SoapHeader;
use Codemitte\ForceToolkit\Soap\Mapping\Base\CallOptions AS CallOptionsData;

/**
 * SoapHeader
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soap
 */
class CallOptions extends SoapHeader
{
    const HEADER_NAME = 'CallOptions';

    /**
     * @var String
     */
    private $client;

    private $defaultNamespace;

    /**
     * Constructor.
     *
     * @param string $namespace
     * @param $client
     * @param $defaultNamespace
     */
    public function __construct($namespace, $client, $defaultNamespace)
    {
        parent::__construct($namespace, self::HEADER_NAME, new CallOptionsData($client, $defaultNamespace), true);

        $this->client = $client;

        $this->defaultNamespace = $defaultNamespace;
    }

    /**
     * @return String
     */
    public function getClient()
    {
        return $this->client;
    }

    public function getDefaultNamespace()
    {
        return $this->defaultNamespace;
    }
}
