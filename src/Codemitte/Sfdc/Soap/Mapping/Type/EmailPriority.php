<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

use Codemitte\Soap\Mapping\Type\GenericType;

class EmailPriority extends GenericType
{
  const Highest = 'Highest';
  const High = 'High';
  const Normal = 'Normal';
  const Low = 'Low';
  const Lowest = 'Lowest';
}
