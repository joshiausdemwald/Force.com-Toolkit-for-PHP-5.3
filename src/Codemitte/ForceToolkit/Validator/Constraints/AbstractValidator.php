<?php
namespace Codemitte\ForceToolkit\Validator\Constraints;

use
    Symfony\Component\Validator\ConstraintValidator,
    Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface
;

abstract class AbstractValidator extends ConstraintValidator
{
    /**
     * @var \Codemitte\ForceToolkit\Metadata\DescribeFormFactoryInterface
     */
    protected $describeFormFactory;

    /**
     * @param DescribeFormFactoryInterface $describeFormFactory
     */
    public function __construct(DescribeFormFactoryInterface $describeFormFactory)
    {
        $this->describeFormFactory = $describeFormFactory;
    }
}
