<?php
namespace Codemitte\ForceToolkit\Test\Soql\Builder;

use
    Codemitte\ForceToolkit\Soap\Client\Connection\SfdcConnection,
    Codemitte\ForceToolkit\Soap\Client\PartnerClient,
    Codemitte\ForceToolkit\Soql\Parser\QueryParser,
    Codemitte\ForceToolkit\Soql\Tokenizer\Tokenizer
;

/**
 * @group QueryParser
 */
class QueryParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PartnerClient
     */
    private static $client;

    /**
     * @var SfdcConnection
     */
    private static $connection;

    private function newParser()
    {
        return new QueryParser(new Tokenizer());
    }

    public function testSimpleSelect()
    {
        $ast = $this->newParser()->parse('SELECT Id, Name FROM Account WHERE Name = "hanswurst"');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\Query', $ast);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\SelectPart', $ast->getSelectPart());
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\FromPart', $ast->getFromPart());
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\WherePart', $ast->getWherePart());
        $this->assertNull($ast->getWithPart());
        $this->assertNull($ast->getHavingPart());
        $this->assertNull($ast->getOrderPart());
        $this->assertNull($ast->getOffset());
        $this->assertNull($ast->getLimit());
    }

    public function testFullSelect()
    {
        $ast = $this->newParser()->parse('
            SELECT
                Id, Name, COUNT(Id)
            FROM
                Account
            WHERE
                Name = "hanswurst"
            GROUP BY
                IsPersonAccount
         ');

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\Query', $ast);
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\SelectPart', $ast->getSelectPart());
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\FromPart', $ast->getFromPart());
        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\AST\WherePart', $ast->getWherePart());
        $this->assertNull($ast->getWithPart());
        $this->assertNull($ast->getHavingPart());
        $this->assertNull($ast->getOrderPart());
        $this->assertNull($ast->getOffset());
        $this->assertNull($ast->getLimit());
    }
}
