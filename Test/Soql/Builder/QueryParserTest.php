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
                Id, Name, COUNT(Id), MAX(dings)
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

    public function testAggregateFunctionNegative1()
    {
        $exThrown = null;

        try
        {
            $this->newParser()->parse('
            SELECT
                Id, Name, COUNT(Id, falseArg)
            FROM
                Account
            WHERE
                Name = "hanswurst"
            GROUP BY
                IsPersonAccount
         ');
        }
        catch(\Exception $e)
        {
            $exThrown = $e;
        }

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Parser\ParseException', $exThrown);
    }

    public function testAggregateFunctionNegative2()
    {
        $exThrown = null;

        try
        {
            $this->newParser()->parse('
            SELECT
                Id, Name, MAX()
            FROM
                Account
            WHERE
                Name = "hanswurst"
            GROUP BY
                IsPersonAccount
         ');
        }
        catch(\Exception $e)
        {
            $exThrown = $e;
        }

        $this->assertInstanceOf('Codemitte\ForceToolkit\Soql\Parser\ParseException', $exThrown);
    }

    public function testArbitraryQueries()
    {
        $this->newParser()->parse("SELECT Id, Name
            FROM Account
            WHERE Name = 'Sandy'");

        $this->newParser()->parse("SELECT count()
            FROM Contact c, c.Account a
            WHERE a.name = 'MyriadPubs'");

        $this->newParser()->parse("SELECT Id FROM Account WHERE Name LIKE 'Ter%'");

        $this->newParser()->parse("SELECT Id FROM Account WHERE Name LIKE 'Ter\%'");

        $this->newParser()->parse("SELECT Id FROM Account WHERE Name LIKE 'Ter\%%'");

        $this->newParser()->parse("SELECT Id
                FROM Account
                WHERE Name LIKE 'Bob\'s BBQ'");

        $this->newParser()->parse("SELECT Name FROM Account WHERE Name like 'A%'");
        $this->newParser()->parse("SELECT Id FROM Contact WHERE Name LIKE 'A%' AND MailingCity='California'");
        $this->newParser()->parse("SELECT Name FROM Account WHERE CreatedDate > 2011-04-26T10:00:00-08:00");
        $this->newParser()->parse("SELECT Amount FROM Opportunity WHERE CALENDAR_YEAR(CreatedDate) = 2011");
        $this->newParser()->parse("SELECT Id
        FROM Case
        WHERE Contact.LastName = null");
    }
}
