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
        $builder2 = new ExpressionBuilder($parser, ExpressionBuilder::CONTEXT_WHERE);
        $builder3 = new ExpressionBuilder($parser, ExpressionBuilder::CONTEXT_WHERE);

        $builder
        ->xpr('FirstName', ExpressionBuilder::OP_EQ, "'hanswurst'")
        ->andNotXpr('LastName', ExpressionBuilder::OP_LIKE, "'Meier%'")
        ->andXpr($builder2
            ->xpr('Salutation', ExpressionBuilder::OP_NEQ, 'NULL')
            ->orXpr('Salutation', ExpressionBuilder::OP_EQ, "'Mr.'")
            ->orXpr($builder3
                ->xpr('SampleMultiPicklist__c', ExpressionBuilder::OP_INCLUDES, "('wert1', 'wert2', 'wert3')")
                ->andXpr('AccountId', ExpressionBuilder::OP_IN, '(SELECT Id FROM Account LIMIT 5)')
            )
        );

        /** @var $expression \Codemitte\ForceToolkit\Soql\AST\LogicalGroup */
        $expression = $builder->getExpression();

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalGroup', $expression);

        $junctions = $expression->getJunctions();
        $this->assertCount(3, $junctions);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[1]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[2]);
        $this->assertFalse($junctions[0]->getIsNot());
        $this->assertTrue($junctions[1]->getIsNot());
        $this->assertFalse($junctions[2]->getIsNot());
        $this->assertEquals(NULL, $junctions[0]->getOperator());
        $this->assertEquals('AND', $junctions[1]->getOperator());
        $this->assertEquals('AND', $junctions[2]->getOperator());
        $conditions = array(
            $junctions[0]->getCondition(),
            $junctions[1]->getCondition(),
            $junctions[2]->getCondition(),
        );

        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalCondition', $conditions[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalCondition', $conditions[1]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalGroup', $conditions[2]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\SoqlExpression', $conditions[0]->getLeft());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\SoqlExpression', $conditions[1]->getLeft());
        $this->assertEquals('FirstName', (string)$conditions[0]->getLeft());
        $this->assertEquals('LastName', (string)$conditions[1]->getLeft());
        $this->assertEquals("'hanswurst'", (string)$conditions[0]->getRight());
        $this->assertEquals("'Meier%'", (string)$conditions[1]->getRight());
        $this->assertEquals('=', $conditions[0]->getOperator());
        $this->assertEquals('LIKE', $conditions[1]->getOperator());

        // SUB-GROUP
        $junctions = $conditions[2]->getJunctions();
        $this->assertCount(3, $junctions);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[1]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[2]);
        $this->assertFalse($junctions[0]->getIsNot());
        $this->assertFalse($junctions[1]->getIsNot());
        $this->assertFalse($junctions[2]->getIsNot());
        $this->assertEquals(NULL, $junctions[0]->getOperator());
        $this->assertEquals('OR', $junctions[1]->getOperator());
        $this->assertEquals('OR', $junctions[2]->getOperator());
        $conditions = array(
            $junctions[0]->getCondition(),
            $junctions[1]->getCondition(),
            $junctions[2]->getCondition(),
        );
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalCondition', $conditions[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalCondition', $conditions[1]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalGroup', $conditions[2]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\SoqlExpression', $conditions[0]->getLeft());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\SoqlExpression', $conditions[1]->getLeft());
        $this->assertEquals('Salutation', (string)$conditions[0]->getLeft());
        $this->assertEquals('Salutation', (string)$conditions[1]->getLeft());
        $this->assertEquals("NULL", (string)$conditions[0]->getRight());
        $this->assertEquals("'Mr.'", (string)$conditions[1]->getRight());
        $this->assertEquals('!=', $conditions[0]->getOperator());
        $this->assertEquals('=', $conditions[1]->getOperator());

        $junctions = $conditions[2]->getJunctions();

        $this->assertCount(2, $junctions);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalJunction', $junctions[1]);
        $this->assertFalse($junctions[0]->getIsNot());
        $this->assertFalse($junctions[1]->getIsNot());
        $this->assertEquals(NULL, $junctions[0]->getOperator());
        $this->assertEquals('AND', $junctions[1]->getOperator());
        $conditions = array(
            $junctions[0]->getCondition(),
            $junctions[1]->getCondition(),
        );
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalCondition', $conditions[0]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\LogicalCondition', $conditions[1]);
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\SoqlExpression', $conditions[0]->getLeft());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\SoqlExpression', $conditions[1]->getLeft());
        $this->assertEquals('SampleMultiPicklist__c', (string)$conditions[0]->getLeft());
        $this->assertEquals('AccountId', (string)$conditions[1]->getLeft());
        $this->assertEquals("('wert1', 'wert2', 'wert3')", (string)$conditions[0]->getRight());
        $this->assertInstanceOf('\Codemitte\ForceToolkit\Soql\AST\Subquery', $conditions[1]->getRight());
    }
}
