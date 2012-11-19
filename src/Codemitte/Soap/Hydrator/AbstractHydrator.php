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

use Codemitte\Soap\Mapping\GenericResult;
use Codemitte\Soap\Mapping\GenericResultCollection;

abstract class AbstractHydrator implements HydratorInterface
{
    /**
     * @param array $list
     * @param null $parentKey
     * @return mixed
     */
    protected abstract function doHydrateList(array $list, $parentKey = null);

    /**
     * @param \stdClass $result
     * @param string|null $parentKey
     * @return mixed
     */
    protected abstract function doHydrate(\stdClass $result, $parentKey = null);

    /**
     * @param $field
     * @param string|null $parentKey
     *
     * @return mixed
     */
    public function hydrate($field, $parentKey = null)
    {
        // stdClass: TRANSFORM IT!
        if($field instanceof \stdClass)
        {
            return $this->doHydrate($field, $parentKey);
        }

        // FINAL NOTE. EXPECTED IS A LIST, EVERYTHING ELSE
        // WILL BE FILTERED OUT AFTER is_object() SECTION
        // MAYBE LIST OR HASHMAP :(
        // THIS SUCKS SOOOOOO MUCH
        // if(is_array($result) && count($result) > 0)
        if(is_array($field))
        {
            return $this->doHydrateList($field, $parentKey);
        }

        // OBJECTS MAPPED BY SOAP CLIENT
        //if(is_object($result))
        //{
        //    $r = new ReflectionObject($result);
        //
        //    // TRAVERSABLE || PUBLIC PROPERTIES
        //    foreach($r->getProperties(~ ReflectionProperty::IS_STATIC) AS $p)
        //    {
        //        $pname = $p->getName();
        //        $ucfname = ucfirst($pname);
        //        $sname = 'set' . $ucfname;
        //        $gname = 'get' . $ucfname;

        //        /* @var $p \ReflectionProperty */
        //        if( ! $p->isDefault() || $p->isPublic())
        //        {
        //            $result->$pname = $this->hydrate($result->$pname);
        //        }

                // GEHT NICHT :(
        //        elseif($r->hasMethod($sname) && $r->hasMethod($gname))
        //        {
        //            $result->$sname($this->hydrate($result->$gname()));
        //        }

                // THE HARD WAY
        //        else
        //        {
        //            $p->setAccessible(true);
        //            $p->setValue($result, $this->hydrate($p->getValue($result)));
        //            $p->setAccessible(false);
        //        }
        //    }
        //}

        // ALL OTHER SCALAR VALUES, RESOURCES, TYPES ETC...
        return $field;
    }

    /**
     * Converts a raw "<any..." xml stream into a php object
     * representation.
     *
     * @param $anyXml
     * @return array
     */
    protected function fromAny($anyXml)
    {
        if( ! is_array($anyXml))
        {
            $anyXml = array($anyXml);
        }

        $res = array();

        foreach($anyXml AS $key => $value)
        {
            // XML STRING, CREATE GenericResult PROPERTIES FROM KEY/VALUE PAIRS
            if(is_string($value))
            {
                $xml = new \SimpleXMLElement(sprintf('<data xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">%s</data>',  $this->stripNamespacePrefix($value)));

                // CASTING MAGICK
                foreach((array)$xml->children() AS $k=> $v)
                {
                    if($v instanceof \SimpleXMLElement)
                    {
                        $atts = $v->attributes('xsi', true);

                        // NIL ATTRIBUTE ...
                        if('true' === (string)$atts['nil'])
                        {
                            $v = null;
                        }
                        else
                        {
                            $v = '';
                        }
                    }
                    $res[$k] = $this->hydrate($v);
                }
            }

            // MAPPED TYPE/CLASS: MERGE AS GenericResult PROPERTIES
            // BEWARE OF DUPLICATE (e.g. salesforce ID) attributes!!
            elseif(is_string($key))
            {
                $res[$key] = $this->hydrate($value);
            }
        }
        return $res;
    }

    /**
     * @param string $xml
     * @return mixed
     * @return string
     */
    private function stripNamespacePrefix($xml)
    {
        return preg_replace('#<(/[ ]*?)?(?:.+?)\:#', '<$1', $xml);
    }
}
