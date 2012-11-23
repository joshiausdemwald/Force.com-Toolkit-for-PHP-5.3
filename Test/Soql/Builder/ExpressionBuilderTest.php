<?php
namespace Codemitte\ForceToolkit\Test\Soql\Builder;

use
    Codemitte\ForceToolkit\Soql\Builder\ExpressionBuilder,
    Codemitte\ForceToolkit\Soql\Parser\QueryParser,
    Codemitte\ForceToolkit\Soql\Tokenizer\Tokenizer
;

/**
 * @group Soql
 */
class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $parser = new QueryParser(new Tokenizer());
        $builder = new ExpressionBuilder($parser, ExpressionBuilder::CONTEXT_WHERE);

        $builder
        ->xpr('FirstName', ExpressionBuilder::OP_EQ, "'hanswurst'")
        ->andXpr('LastName', ExpressionBuilder::OP_LIKE, "'Meier%'")
        ->andXpr($builder
            ->xpr('Salutation', ExpressionBuilder::OP_NEQ, 'NULL')
            ->orXpr('Salutation', ExpressionBuilder::OP_EQ, "'Mr.'")
            ->orXpr($builder
                ->xpr('SampleMultiPicklist__c', ExpressionBuilder::OP_INCLUDES, "('wert1', 'wert2', 'wert3')")
                ->andXpr('AccountId', ExpressionBuilder::OP_IN, '(SELECT Id FROM Account LIMIT 5)')
            )
        );
    }
}
