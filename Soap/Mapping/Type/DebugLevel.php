<?php
namespace Codemitte\ForceToolkit\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

class DebugLevel extends GenericType
{
  const None = 'None';
  const DebugOnly = 'DebugOnly';
  const Db = 'Db';

    /**
     * The target namespace of the type.
     *
     * @return string
     */
    public static function getURI()
    {
        return 'urn:enterprise.soap.sforce.com';
    }
}
