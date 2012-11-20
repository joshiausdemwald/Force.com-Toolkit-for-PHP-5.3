<?php
namespace Codemitte\ForceToolkit\Form\Type;

use Symfony\Component\Form\FormTypeInterface;

interface SfdcTypeInterface extends FormTypeInterface
{
    /**
     * @return \Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface
     */
    public function getDescribeFormFactory();
}
