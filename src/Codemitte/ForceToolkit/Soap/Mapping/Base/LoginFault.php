<?php
namespace Codemitte\ForceToolkit\Soap\Mapping\Base;

use \RuntimeException;

use Codemitte\Soap\Mapping\ClassInterface;

class LoginFault extends RuntimeException implements ClassInterface { }
