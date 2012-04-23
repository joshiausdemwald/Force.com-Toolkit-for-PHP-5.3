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

namespace Codemitte\Soap\Client\Decorator;

use \SoapVar;
use \ReflectionObject;
use \ReflectionProperty;

use Codemitte\Soap\Mapping\Type\TypeInterface;

/**
 * DecoratorInterface
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Soap
 * @subpackage Client
 */
final class SoapParamDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $uri;

    /**
     * Constructor.
     *
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Decorates a type/object (part of a soap request) to fit
     * soap "specialities". Mainly it is used to transform simple
     * types into SoapVar instances.
     *
     * @todo Generate Proxy classes instead of using reflection.
     *
     * @param object|array $type
     *
     * @return object|array $type
     */
    public function decorate($type)
    {
        if($type instanceof SoapVar)
        {
            $type->enc_value = $this->decorate($type->enc_value);
        }
        elseif(is_array($type))
        {
            foreach($type AS $key => $value)
            {
                $type[$key] = $this->decorate($value);
            }
        }
        elseif($type instanceof \stdClass)
        {
            foreach($type AS $key => $value)
            {
                $type->$key = $this->decorate($value);
            }
        }
        elseif(is_object($type))
        {
            $r = new ReflectionObject($type);

            // @TODO: REFACTOR CODE-DUPLICATION, @SEE AbstractHydrator
            foreach($r->getProperties(~ ReflectionProperty::IS_STATIC) AS $p)
            {
                $pname = $p->getName();
                $ucfname = ucfirst($pname);
                $sname = 'set' . $ucfname;
                $gname = 'get' . $ucfname;

                /* @var $p ReflectionProperty */
                if( ! $p->isDefault() || $p->isPublic())
                {
                    $type->$pname = $this->decorate($type->$pname);
                }

                // GEHT NICHT :(
                elseif($r->hasMethod($sname) && $r->hasMethod($gname))
                {
                    $type->$sname($this->decorate($type->$gname()));
                }

                // THE HARD WAY
                else
                {
                    $p->setAccessible(true);
                    $p->setValue($type, $this->decorate($p->getValue($type)));
                    $p->setAccessible(false);
                }
            }

            // CONVERT!
            if($type instanceof TypeInterface)
            {
                $namespace = $type->getURI();

                if(null === $namespace)
                {
                    $namespace = $this->getUri();
                }

                $type = new SoapVar($type, SOAP_ENC_OBJECT, $this->extractClassname($type), $namespace);
            }
        }

        return $type;
    }

    /**
     * Returns the target namespace to put into SoapVar
     * instances.
     *
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Extracts the classname out of a fully
     * qualified namespace name.
     *
     * @param object $object
     *
     * @return string
     */
    protected function extractClassname($object)
    {
        $pathname = get_class($object);

        if(false === ($pos = (strrpos($pathname, '\\'))))
        {
            return $pathname;
        }
        return substr($pathname, $pos + 1);
    }
}
