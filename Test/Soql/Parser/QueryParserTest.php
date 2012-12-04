<?php
namespace Codemitte\ForceToolkit\Test\Soql\Parser;

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
        $this->newParser()->parse("SELECT AccountId
FROM Event
WHERE ActivityDate != null");

        $this->newParser()->parse("SELECT Company, toLabel(Recordtype.Name) FROM Lead");
        $this->newParser()->parse("SELECT Company, toLabel(Status)
FROM Lead
WHERE toLabel(Status) = 'le Draft'");

        $this->newParser()->parse("SELECT Id, MSP1__c FROM CustObj__c WHERE MSP1__c = 'AAA;BBB'");
        $this->newParser()->parse("SELECT Id, MSP1__c from CustObj__c WHERE MSP1__c includes ('AAA;BBB','CCC')");
        $this->newParser()->parse("SELECT Id
FROM Event
WHERE What.Type IN ('Account', 'Opportunity')");

        $this->newParser()->parse("SELECT Name FROM Account
WHERE BillingState IN ('California', 'New York')");

        $this->newParser()->parse("SELECT Id, Name
FROM Account
WHERE Id IN
  ( SELECT AccountId
    FROM Opportunity
    WHERE StageName = 'Closed Lost'
  )");

        $this->newParser()->parse("SELECT Id
FROM Task
WHERE WhoId IN
  (
    SELECT Id
    FROM Contact
    WHERE MailingCity = 'Twin Falls'
  )");

        $this->newParser()->parse("SELECT Id
FROM Account
WHERE Id NOT IN
  (
    SELECT AccountId
    FROM Opportunity
    WHERE IsClosed = false
  )");

        $this->newParser()->parse("SELECT Id
FROM Opportunity
WHERE AccountId NOT IN
  (
    SELECT AccountId
    FROM Contact
    WHERE LeadSource = 'Web'
  )");

        $this->newParser()->parse("SELECT Id, Name
FROM Account
WHERE Id IN
  (
    SELECT AccountId
    FROM Contact
    WHERE LastName LIKE 'apple%'
  )
  AND Id IN
  (
    SELECT AccountId
    FROM Opportunity
    WHERE isClosed = false
  )");

        $this->newParser()->parse("SELECT Id, (SELECT Id from OpportunityLineItems)
FROM Opportunity
WHERE Id IN
  (
    SELECT OpportunityId
    FROM OpportunityLineItem
    WHERE totalPrice > 10000
  )");

        $this->newParser()->parse("SELECT Id
 FROM Idea
 WHERE (Id IN (SELECT ParentId FROM Vote WHERE CreatedDate > LAST_WEEK AND Parent.Type='Idea'))");

        $this->newParser()->parse("SELECT Id, Name
FROM Account
WHERE Id IN
  (
    SELECT AccountId
    FROM Contact
    WHERE LastName LIKE 'Brown_%'
  )");

        $this->newParser()->parse("SELECT Id, Name
FROM Account
WHERE Id IN
  (
    SELECT ParentId
    FROM Account
    WHERE Name = 'myaccount'
  )");

        $this->newParser()->parse("SELECT Id, Name
FROM Account
WHERE Parent.Name = 'myaccount'");

        $this->newParser()->parse("SELECT Id
 FROM Idea
 WHERE (Idea.Title LIKE 'Vacation%')
AND (Idea.LastCommentDate > YESTERDAY)
AND (Id IN (SELECT ParentId FROM Vote
            WHERE CreatedById = '005x0000000sMgYAAU'
             AND Parent.Type='Idea'))");

        $this->newParser()->parse("SELECT Id
 FROM Idea
 WHERE
  ((Idea.Title LIKE 'Vacation%')
  AND (CreatedDate > YESTERDAY)
  AND (Id IN (SELECT ParentId FROM Vote
              WHERE CreatedById = '005x0000000sMgYAAU'
               AND Parent.Type='Idea')
  )
  OR (Idea.Title like 'ExcellentIdea%'))");

        $this->newParser()->parse("SELECT Name
FROM Account
ORDER BY Name DESC NULLS LAST");

        $this->newParser()->parse("SELECT Id, CaseNumber, Account.Id, Account.Name
FROM Case
ORDER BY Account.Name");

        $this->newParser()->parse("SELECT Name
FROM Account
WHERE industry = 'media'
ORDER BY BillingPostalCode ASC NULLS LAST LIMIT 125");

        $this->newParser()->parse("SELECT Name
FROM Account
WHERE Industry = 'Media' LIMIT 125");

        $this->newParser()->parse("SELECT MAX(CreatedDate)
FROM Account LIMIT 1");

        $this->newParser()->parse("SELECT Name
FROM Merchandise__c
WHERE Price__c > 5.0
ORDER BY Name
LIMIT 100
OFFSET 10");

        $this->newParser()->parse("SELECT Name, Id
FROM Merchandise__c
ORDER BY Name
LIMIT 100
OFFSET 0");

        $this->newParser()->parse("SELECT Name, Id
FROM Merchandise__c
ORDER BY Name
LIMIT 100
OFFSET 100");

        $this->newParser()->parse("SELECT Name
FROM Merchandise__c
ORDER BY Name
OFFSET 10");

        $this->newParser()->parse("SELECT Title FROM KnowledgeArticleVersion WHERE PublishStatus='online' WITH DATA CATEGORY Geography__c ABOVE usa__c");

        $this->newParser()->parse("SELECT Id FROM UserProfileFeed WITH UserId='005D0000001AamR' ORDER BY CreatedDate DESC, Id DESC LIMIT 20");

        $this->newParser()->parse("SELECT Title FROM KnowledgeArticleVersion WHERE PublishStatus='online' WITH DATA CATEGORY Geography__c ABOVE usa__c");

        $this->newParser()->parse("SELECT Title FROM Question WHERE LastReplyDate > 2005-10-08T01:02:03Z WITH DATA CATEGORY Geography__c AT (usa__c, uk__c)");

        $this->newParser()->parse("SELECT UrlName FROM KnowledgeArticleVersion WHERE PublishStatus='draft' WITH DATA CATEGORY Geography__c AT usa__c AND Product__c ABOVE_OR_BELOW mobile_phones__c");

        $this->newParser()->parse("SELECT Title FROM Question WHERE LastReplyDate < 2005-10-08T01:02:03Z WITH DATA CATEGORY Product__c AT mobile_phones__c");
        $this->newParser()->parse("SELECT Title, Summary FROM KnowledgeArticleVersion WHERE PublishStatus='Online' AND Language = 'en_US' WITH DATA CATEGORY Geography__c ABOVE_OR_BELOW europe__c AND Product__c BELOW All__c");
        $this->newParser()->parse("SELECT Id, Title FROM Offer__kav WHERE PublishStatus='Draft' AND Language = 'en_US' WITH DATA CATEGORY Geography__c AT (france__c,usa__c) AND Product__c ABOVE dsl__c");

        $this->newParser()->parse("SELECT LeadSource, COUNT(Name)
FROM Lead
GROUP BY LeadSource");

        $this->newParser()->parse("SELECT LeadSource
FROM Lead
GROUP BY LeadSource");

        $this->newParser()->parse("SELECT Name, Max(CreatedDate)
FROM Account
GROUP BY Name
LIMIT 5");

        $this->newParser()->parse("SELECT Name n, MAX(Amount) max
FROM Opportunity
GROUP BY Name");

        $this->newParser()->parse("SELECT Name, MAX(Amount), MIN(Amount)
FROM Opportunity
GROUP BY Name");

        $this->newParser()->parse("SELECT Name, MAX(Amount), MIN(Amount) min, SUM(Amount)
FROM Opportunity
GROUP BY Name");

        $this->newParser()->parse("SELECT LeadSource, COUNT(Name) cnt
FROM Lead
GROUP BY ROLLUP(LeadSource)");

        $this->newParser()->parse("SELECT Status, LeadSource, COUNT(Name) cnt
FROM Lead
GROUP BY ROLLUP(Status, LeadSource)
");

        $this->newParser()->parse("SELECT LeadSource, Rating,
    GROUPING(LeadSource) grpLS, GROUPING(Rating) grpRating,
    COUNT(Name) cnt
FROM Lead
GROUP BY ROLLUP(LeadSource, Rating)");

        $this->newParser()->parse("SELECT Type, BillingCountry,
    GROUPING(Type) grpType, GROUPING(BillingCountry) grpCty,
    COUNT(id) accts
FROM Account
GROUP BY CUBE(Type, BillingCountry)
ORDER BY GROUPING(Type), GROUPING(BillingCountry)");

        $this->newParser()->parse("SELECT LeadSource, COUNT(Name)
FROM Lead
GROUP BY LeadSource");

        $this->newParser()->parse("SELECT LeadSource, COUNT(Name)
FROM Lead
GROUP BY LeadSource
HAVING COUNT(Name) > 100");

        $this->newParser()->parse("SELECT Name, Count(Id)
FROM Account
GROUP BY Name
HAVING Count(Id) > 1");

        $this->newParser()->parse("SELECT LeadSource, COUNT(Name)
FROM Lead
GROUP BY LeadSource
HAVING COUNT(Name) > 100 and LeadSource > 'Phone'
");

        $this->newParser()->parse("SELECT Name FROM Account
WHERE CreatedById IN
    (
    SELECT
        TYPEOF Owner
            WHEN User THEN Id
            WHEN Group THEN CreatedById
        END
    FROM CASE
    )");

        $this->newParser()->parse("SELECT
    TYPEOF What
        WHEN Account THEN Phone
        ELSE Name
    END
FROM Event
WHERE CreatedById IN
    (
    SELECT CreatedById
    FROM Case
    )");

        $this->newParser()->parse("SELECT
  TYPEOF What
    WHEN Account THEN Phone, NumberOfEmployees
    WHEN Opportunity THEN Amount, CloseDate
    ELSE Name, Email
  END
FROM Event");

        $this->newParser()->parse("SELECT AVG(Amount)
FROM Opportunity");

        $this->newParser()->parse("SELECT CampaignId, AVG(Amount)
FROM Opportunity
GROUP BY CampaignId");

        $this->newParser()->parse("SELECT CampaignId, AVG(Amount)
FROM Opportunity
GROUP BY CampaignId");

        $this->newParser()->parse("SELECT COUNT()
FROM Account
WHERE Name LIKE 'a%'");

        $this->newParser()->parse("SELECT COUNT(Id)
FROM Account
WHERE Name LIKE 'a%'");

        $this->newParser()->parse("SELECT COUNT_DISTINCT(Company)
FROM Lead");

        $this->newParser()->parse("SELECT MIN(CreatedDate), FirstName, LastName
FROM Contact
GROUP BY FirstName, LastName");

        $this->newParser()->parse("SELECT Name, MAX(BudgetedCost)
FROM Campaign
GROUP BY Name");

        $this->newParser()->parse("SELECT SUM(Amount)
FROM Opportunity
WHERE IsClosed = false AND Probability > 60");

        $this->newParser()->parse("SELECT COUNT()
FROM Account
WHERE Name LIKE 'a%'");

        $this->newParser()->parse("SELECT COUNT()
FROM Contact, Contact.Account
WHERE Account.Name = 'MyriadPubs'");

        $this->newParser()->parse("SELECT COUNT(Id)
FROM Account
WHERE Name LIKE 'a%'");

        $this->newParser()->parse("SELECT COUNT()
FROM Account
WHERE Name LIKE 'a%'");

        $this->newParser()->parse("SELECT COUNT(Id)
FROM Account
WHERE Name LIKE 'a%'");

        $this->newParser()->parse("SELECT COUNT(Id), COUNT(CampaignId)
FROM Opportunity");

        $this->newParser()->parse("SELECT LeadSource, COUNT(Name)
FROM Lead
GROUP BY LeadSource");

        $this->newParser()->parse("SELECT CALENDAR_YEAR(CreatedDate), SUM(Amount)
FROM Opportunity
GROUP BY CALENDAR_YEAR(CreatedDate)");

        $this->newParser()->parse("SELECT CreatedDate, Amount
FROM Opportunity
WHERE CALENDAR_YEAR(CreatedDate) = 2009");

        $this->newParser()->parse("SELECT CALENDAR_YEAR(CloseDate)
FROM Opportunity
GROUP BY CALENDAR_YEAR(CloseDate)");

        $this->newParser()->parse("SELECT HOUR_IN_DAY(convertTimezone(CreatedDate)), SUM(Amount)
FROM Opportunity
GROUP BY HOUR_IN_DAY(convertTimezone(CreatedDate))");

        $this->newParser()->parse("SELECT Id, convertCurrency(AnnualRevenue)
FROM Account");

        $this->newParser()->parse("SELECT Id, Name
FROM Opportunity
WHERE Amount > USD5000");

        $this->newParser()->parse("SELECT Name, MAX(Amount)
FROM Opportunity
GROUP BY Name
HAVING MAX(Amount) > 10000");
    }

    public function testParseKeywordsAsExpressions()
    {
        $this->newParser()->parse("SELECT MAX(dingsda) FROM bums HAVING MAX(dingsda) > 100");

        $this->newParser()->parse("SELECT min, MAX(dingsda) max FROM group HAVING MAX(dingsda) > 100");
    }
}
