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

namespace Codemitte\Soap\Mapping\Type;

use \BadMethodCallException;

/**
 * GenericType
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
 * @subpackage Mapping
 */
abstract class GenericType implements TypeInterface
{
    protected $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @var string $xml
     */
    public static function fromXml($xml_string)
    {
        $xml = simplexml_load_string($xml_string);

        $name = get_called_class();

        return new $name((string)$xml[0]);
    }

    /**
     * <sf:Id xmlns:sf="urn:sobject.enterprise.soap.sforce.com">a03R0000001fiE8IAI</sf:Id>
     *
     * @static
     * @param $value
     * @return string
     */
    public static function toXml($value)
    {
        $name = $pathname = get_called_class();

        if(false !== ($pos = strrpos($pathname, '\\')))
        {
            $name = substr($pathname, $pos + 1);
        }

        return '<' . $name . '>' . $value . '</' . $name . '>';
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Convinience method. if left NULL, the "global" target
     * namespace will be taken as defined for the current connection.
     *
     * @return null
     */
    public static function getURI()
    {
        return null;
    }
}
