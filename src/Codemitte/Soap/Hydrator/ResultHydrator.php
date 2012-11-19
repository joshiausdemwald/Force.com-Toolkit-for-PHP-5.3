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

namespace Codemitte\Soap\Hydrator;

use \stdClass;

use Codemitte\Soap\Mapping\GenericResult;
use Codemitte\Soap\Mapping\GenericResultCollection;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
 * @subpackage Hydrator
 */
class ResultHydrator extends AbstractHydrator
{
    /**
     * @param array $list
     * @param null $parentKey
     * @return \Codemitte\Soap\Mapping\GenericResultCollection
     */
    protected function doHydrateList(array $list, $parentKey = null)
    {
        foreach($list AS $key => $value)
        {
            $list[$key] = $this->hydrate($value, $key);
        }
        return new GenericResultCollection($list);
    }

    /**
     * @param \stdClass $result
     * @param string null $parentKey
     * @return \Codemitte\Soap\Mapping\GenericResult
     */
    protected function doHydrate(\stdClass $result, $parentKey = null)
    {
        $data = array();

        foreach($result AS $name => $prop)
        {
            // WORKAROUND FOR "ANY"-FIELDS; ARBITRARY XML.
            if('any' === $name)
            {
                $data = array_merge($data, $this->fromAny($prop));
            }
            else
            {
                // any => 0 => "<sf:s...>", "Account" => Object { .... }
                $data[$name] = $this->hydrate($prop, $name);
            }
        }
        return new GenericResult($data);
    }
}
