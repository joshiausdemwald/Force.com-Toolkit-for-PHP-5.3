<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

class layoutComponentType extends GenericType
{
  const Field = 'Field';
  const Separator = 'Separator';
  const SControl = 'SControl';
  const EmptySpace = 'EmptySpace';

  public static function getName()
  {
      return 'layoutComponentType';
  }
}
