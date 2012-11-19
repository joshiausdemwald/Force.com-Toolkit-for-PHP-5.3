<?php
namespace Codemitte\ForceToolkit\Validator\Constraints;

use
    Symfony\Component\Validator\Constraint,
    Codemitte\ForceToolkit\Form\Model\PicklistChoiceList
;

class PicklistValidator extends AbstractValidator
{
    /**
     * @param $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return bool
     * @throws \Exception
     */
    function validate($value, Constraint $constraint)
    {
        if( ! $value)
        {
            return;   // REQUIRED CONSTRAINT (!)
        }

        try
        {
            $describeFormResult = $this->describeFormFactory->getDescribe
            (
                $constraint->sObjectType,
                $constraint->recordTypeId
            );

            $field = $describeFormResult->getField($constraint->fieldname);

            $picklistChoiceList = new PicklistChoiceList(
                $field,
                isset($options['filter']) ? $options['filter'] : null
            );

            $keys = $picklistChoiceList->getChoices();

            // MULTI-PICKLIST
            if( ! is_array($value))
            {
                $value = array($value);
            }
            /*if(count(array_intersect($keys, $value)) === count($value))
            {
                return true;
            }*/

            foreach ($value as $v)
            {
                if ( ! in_array($v, $keys))
                {
                    $this->context->addViolation($constraint->multipleMessage, array('{{ value }}' => $v));
                }
            }
        }
        catch(\Exception $e)
        {
            throw $e;
        }
    }
}
