<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

class DebugLevel extends GenericType
{
  const None = 'None';
  const DebugOnly = 'DebugOnly';
  const Db = 'Db';
}
