<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

class DebugLevel extends GenericType
{
  const None = 'None';
  const DebugOnly = 'DebugOnly';
  const Db = 'Db';

    public static function getName()
    {
        return 'DebugLevel';
    }

}
