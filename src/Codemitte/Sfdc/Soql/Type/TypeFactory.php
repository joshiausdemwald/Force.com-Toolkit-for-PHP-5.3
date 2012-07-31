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

namespace Codemitte\Sfdc\Soql\Type;

use \Traversable;

/**
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soql
 */
class TypeFactory
{
    /**
     * Takes skalar params and/or arrays and converts
     * it into salesforce types.
     *
     * @param mixed|null $param
     *
     * @return TypeInterface $salesforceType
     */
    public function create($param = null)
    {
        if(null === $param)
        {
            return new NullValue($param);
        }
        elseif($param instanceof TypeInterface)
        {
            return $param;
        }
        elseif(is_bool($param))
        {
            return new Boolean($param);
        }
        elseif(is_string($param))
        {
            if(preg_match('#^[0-9][0-9][0-9][0-9]\-[0-9][0-9]\-[0-9][0-9]#', $param))
            {
                try
                {
                    if(10 === strlen($param))
                    {
                        return new Date($param);
                    }
                    else
                    {
                        return new DateTime($param);
                    }
                }
                catch(\Exception $e)
                {
                     // DO NOTHING
                }
            }
            return new String($param);
        }
        elseif(is_array($param) || $param instanceof Traversable)
        {
            $retVal = array();

            foreach($param AS $key => $value)
            {
                $retVal[$key] = $this->create($value);
            }
            return new ArrayType($retVal);
        }
        elseif(is_int($param) || is_float($param))
        {
            return new Number($param);
        }
        elseif(is_object($param))
        {
            /* @var $param  \DateTime */
            if($param instanceof \DateTime)
            {
                return new DateTime($param->format(\DateTime::W3C));
            }
        }
    }
}
