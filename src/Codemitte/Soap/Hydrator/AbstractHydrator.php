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
use \ReflectionObject;
use \ReflectionProperty;

abstract class AbstractHydrator implements HydratorInterface
{
    public abstract function doHydrateList($list);

    public abstract function doHydrate($result);

    public function hydrate($result)
    {
        // stdClass: TRANSFORM IT!
        if($result instanceof stdClass)
        {
            return $this->doHydrate($result);
        }

        // LIST RESULT, TRANSFORM IT!
        if(is_array($result))
        {
            return $this->doHydrateList($result);
        }

        // OBJECTS MAPPED BY SOAP CLIENT
        if(is_object($result))
        {
            $r = new ReflectionObject($result);

            // TRAVERSABLE || PUBLIC PROPERTIES
            foreach($r->getProperties(~ ReflectionProperty::IS_STATIC) AS $p)
            {
                $pname = $p->getName();
                $ucfname = ucfirst($pname);
                $sname = 'set' . $ucfname;
                $gname = 'get' . $ucfname;

                /* @var $p \ReflectionProperty */
                if( ! $p->isDefault() || $p->isPublic())
                {
                    $result->$pname = $this->hydrate($result->$pname);
                }

                // GEHT NICHT :(
                elseif($r->hasMethod($sname) && $r->hasMethod($gname))
                {
                    $result->$sname($this->hydrate($result->$gname()));
                }

                // THE HARD WAY
                else
                {
                    $p->setAccessible(true);
                    $p->setValue($result, $this->hydrate($p->getValue($result)));
                    $p->setAccessible(false);
                }
            }
        }

        // ALL OTHER SCALAR VALUES, RESOURCES, TYPES ETC...
        return $result;
    }
}
