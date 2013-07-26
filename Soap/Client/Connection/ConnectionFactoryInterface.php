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
use Psr\Log\LoggerInterface;

/**
 * ConnectionFactory
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage EPS
 */
interface ConnectionFactoryInterface
{
    /**
     * Returns or creates and returns a new
     * client instance based on the current
     * session's locale.
     *
     * @throws \InvalidArgumentException
     * @return SfdcConnectionInterfacetest
     */
    public function getInstance();

    /**
     * Returns the current symfony session locale.
     *
     * @return mixed|null
     */
    public function getCurrentLocale();

    /**
     * @param string|null $locale
     * @return array|null
     */
    public function getUserConfig($locale = null);

    /**
     * @return null|LoggerInterface
     */
    public function getLogger();

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null);
}
