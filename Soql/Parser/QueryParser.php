<?php
/**
 * Copyright (C) 2012 code mitte GmbH - Zeughausstr. 28-38 - 50667 Cologne/Germany
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so, subject
 * to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Codemitte\ForceToolkit\Soql\Parser;

use Codemitte\ForceToolkit\Soql\Tokenizer\TokenizerInterface;
use Codemitte\ForceToolkit\Soql\Tokenizer\TokenizerException;
use Codemitte\ForceToolkit\Soql\Tokenizer\TokenType;

use
    Codemitte\ForceToolkit\Soql\AST
;

/**
 * QueryParser
 *
 * @author Johannes Heinen <johannes.heinen@code-mitte.de>
 * @copyright 2012 code mitte GmbH, Cologne, Germany
 * @package Sfdc
 * @subpackage Soql
 *
 * @todo: Regard "typeof", new Salesforce API 26.0 beta feature
 */
class QueryParser implements QueryParserInterface
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    private static $DATA_CATEGORY_COMPARISON_OPERATORS = array(
        'AT',
        'ABOVE',
        'BELOW',
        'ABOVE_OR_BELOW'
    );

    const BOOL_FALSE = 'FALSE', BOOL_TRUE = 'TRUE', NIL = 'NULL';

    /**
     * @var array
     */
    private static $DATE_CONSTANTS = array(
        'YESTERDAY',
        'TODAY',
        'TOMORROW',
        'LAST_WEEK',
        'THIS_WEEK',
        'NEXT_WEEK',
        'LAST_MONTH',
        'THIS_MONTH',
        'NEXT_MONTH',
        'LAST_90_DAYS',
        'NEXT_90_DAYS',
        'THIS_QUARTER',
        'LAST_QUARTER',
        'NEXT_QUARTER',
        'THIS_YEAR',
        'LAST_YEAR',
        'NEXT_YEAR',
        'THIS_FISCAL_QUARTER',
        'LAST_FISCAL_QUARTER',
        'NEXT_FISCAL_QUARTER',
        'THIS_FISCAL_YEAR',
        'LAST_FISCAL_YEAR',
        'NEXT_FISCAL_YEAR',

    );

    private static $DATE_FORMULAS = array(
        'LAST_N_DAYS',
        'NEXT_N_DAYS',
        'NEXT_N_YEARS',
        'LAST_N_YEARS',
        'NEXT_N_FISCAL_​QUARTERS',
        'LAST_N_FISCAL_​QUARTERS',
        'NEXT_N_FISCAL_​YEARS',
        'LAST_N_FISCAL_​YEARS',
        'NEXT_N_QUARTERS',
        'LAST_N_QUARTERS',
    );

    private static $AGGREGATE_FUNCTIONS = array(
        'COUNT',
        'COUNT_DISTINCT',
        'MAX',
        'MIN',
        'AVG',
        'SUM'
    );

    private static $DATE_FUNCTIONS = array(
        'CALENDAR_MONTH',
        'CALENDAR_QUARTER',
        'CALENDAR_YEAR',
        'DAY_IN_MONTH',
        'DAY_IN_WEEK',
        'DAY_IN_YEAR',
        'DAY_ONLY',
        'FISCAL_MONTH',
        'FISCAL_QUARTER',
        'FISCAL_YEAR',
        'HOUR_IN_DAY',
        'WEEK_IN_MONTH',
        'WEEK_IN_YEAR',
    );

    private static $ALLOWED_DATE_FUNCTION_FUNCTIONS = array('CONVERTTIMEZONE');

    /**
     * @var array
     */
    private static $SELECT_FUNCTIONS = array
    (
        'GROUPING',
        'TOLABEL',
        'CONVERTCURRENCY'
    );

    /**
     * http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_soql_select_tolabel.htm
     * The toLabel() method cannot be used with ORDER BY. Salesforce always uses the picklist’s defined order,
     * just like reports. Also, you can’t use toLabel() in the WHERE clause for division or currency ISO code
     * picklists.
     */
    private static $ORDER_BY_FUNCTIONS = array(
       'CONVERTCURRENCY',
       'GROUPING'
    );

    /**
     * Constructor.
     *
     * @param TokenizerInterface|null $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer = null)
    {
        if(null === $tokenizer)
        {
            $tokenizer = new QueryTokenizer();
        }
        $this->tokenizer = $tokenizer;
    }

    /**
     *
     * @param string $soql
     * @param string $soql
     * @param array $parameters
     *
     * @return string
     * @return string|void
     */
    public function parse($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseQuery();
    }

    /**
     * @param string $soql
     * @return array<SelectField>
     */
    public function parseSelectSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseSelectFields();
    }

    /**
     * @param $soql
     * @return \Codemitte\ForceToolkit\Soql\AST\FromPart
     */
    public function parseFromSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseFromField();
    }

    /**
     * @param $soql
     * @return array<LogicalJunction>
     */
    public function parseWhereSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseWhereConditions();
    }

    /**
     * @param $soql
     * @return array<LogicalJunction>
     */
    public function parseWithSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseWithConditions();
    }

    /**
     * @param $soql
     * @return array<GroupByField>
     */
    public function parseGroupSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseGroupByExpression();
    }

    /**
     * @param $soql
     * @return array<LogicalJunction>
     */
    public function parseHavingSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseHavingConditions();
    }

    /**
     * @param $soql
     * @return array<LogicalJunction>
     */
    public function parseOrderBySoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseOrderByExpression();
    }

    public function parseLeftWhereSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseWhereLeft();
    }

    public function parseLeftHavingSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseHavingLeft();
    }

    public function parseRightWhereSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseWhereRight();
    }

    public function parseRightHavingSoql($soql)
    {
        $this->tokenizer->setInput($soql);

        $this->tokenizer->expect(TokenType::BOF);

        return $this->parseHavingRight();
    }

    /**
     * SELECT fieldList
     * FROM objectType
     * [WHERE condition]
     * [WITH [DATA CATEGORY] filter]
     * [GROUP BY fieldlist] | [GROUP BY ROLLUP|CUBE (fieldSubtotalGroupByList)]
     * [HAVING condition]
     * [ORDER BY fieldList ASC|DESC ? NULLS FIRST|LAST ?]
     * [LIMIT ?]
     * [OFFSET ?]
     * @TODO: VALIDATE, SPLIT AND MAP TYPES TO INCOMING VARIABLES (INTROSPECT)
     *
     * @return Query
     */
    private function parseQuery()
    {
        $retVal = new AST\Query;

        $retVal->setSelectPart($this->parseSelect());

        $retVal->setFromPart($this->parseFrom());

        if($this->tokenizer->isKeyword('where'))
        {
            $retVal->setWherePart($this->parseWhere());
        }

        if($this->tokenizer->isKeyword('with'))
        {
            $retVal->setWithPart($this->parseWith());
        }

        if($this->tokenizer->isKeyword('group'))
        {
            $retVal->setGroupPart($this->parseGroup());
        }

        if($this->tokenizer->isKeyword('having'))
        {
            $retVal->setHavingPart($this->parseHaving());
        }

        if($this->tokenizer->isKeyword('order'))
        {
            $retVal->setOrderPart($this->parseOrder());
        }

        if($this->tokenizer->isKeyword('limit'))
        {
            $retVal->setLimit($this->parseLimit());
        }

        if($this->tokenizer->isKeyword('offset'))
        {
            $retVal->setOffset($this->parseOffset());
        }

        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\SelectPart
     */
    private function parseSelect()
    {
        $retVal = new AST\SelectPart();

        $this->tokenizer->expectKeyword('select');

        $retVal->addSelectFields($this->parseSelectFields());

        return $retVal;
    }

    /**
     * @return array
     */
    private function parseSelectFields()
    {
        $selectFields = array();

        /* (SELECT, FUNCTION() [alias], fieldname [alias])
           SELECT COUNT() special case
           SELECT [...] TYPEOF fieldname WHEN type1 THEN fieldlist1 [WHEN type2 THEN fieldlist 2] [ELSE elsefieldlist] END
             - SELECT TYPEOF [...] is only valid in outer SELECT clause
             - NO GROUP BY [ROLLUP|CUBE] AND HAVING ALLOWED
        */
        while(true)
        {
            $selectFields[] = $this->parseSelectField();

            if($this->tokenizer->is(TokenType::COMMA))
            {
                $this->tokenizer->readNextToken();

                continue;
            }
            break;
        }
        return $selectFields;
    }

    /**
     * COUNT()
     * toLabel(custom__c)
     * custom__c
     * a.custom__c
     * a.custom__r.custom__c
     * Account.Id
     * ID
     */
    private function parseSelectField()
    {
        $retVal = null;

        // IS SUBSELECT
        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            $this->tokenizer->readNextToken(); // "SELECT"

            $retVal = new AST\Subquery($this->parseQuery());

            $this->tokenizer->expect(TokenType::RIGHT_PAREN);
        }

        // TYPEOF, [WHEN, ELSE] KEYWORD
        elseif($this->tokenizer->is(TokenType::KEYWORD))
        {
            $keyword = strtoupper($this->tokenizer->getTokenValue());

            switch($keyword)
            {
                case 'TYPEOF':
                    $this->tokenizer->readNextToken(); // ADVANCE TO "WHEN"

                    $retVal = $this->parseSelectTypeofExpression();

                    // EXPECT "END", ADVANCE TO "FROM"-CLAUSE
                    $this->tokenizer->expectKeyword('end');

                    // DIRECT RETURN, TYPEOF PART HAS NO ALIAS --- hackish
                    return $retVal;
                break;
                default:
                    throw new ParseException(sprintf('Unexpected keyword "%s", expecting "TYPEOF", "LEFT_PAREN" or "FIELDNAME".', $keyword), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
            }
        }

        // FIELD
        elseif($this->tokenizer->is(TokenType::EXPRESSION))
        {
            $name = $this->tokenizer->getTokenValue();

            $uppercaseName  = strtoupper($this->tokenizer->getTokenValue());

            $oldPos = $this->tokenizer->getLinePos();
            $oldLine = $this->tokenizer->getLine();

            $this->tokenizer->expect(TokenType::EXPRESSION);

            if($this->tokenizer->is(TokenType::LEFT_PAREN))
            {
                if(in_array($uppercaseName, self::$AGGREGATE_FUNCTIONS))
                {
                    $retVal = new AST\SelectField($this->parseSelectAggregateFunction($name));
                }
                else if(in_array($uppercaseName, self::$SELECT_FUNCTIONS))
                {
                    $retVal = new AST\SelectField($this->parseSelectFunction($name));
                }
                else
                {
                    throw new ParseException(sprintf('Unknown function "%s"', $uppercaseName), $oldLine, $oldPos, $this->tokenizer->getInput());
                }
            }
            else
            {
                $retVal = new AST\SelectField($name);
            }
        }
        else
        {
            throw new ParseException(sprintf('Unexpected token "%s", expecting Expression or Left Paren', $this->tokenizer->getTokenType()), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }

        // ALIAS
        if($this->tokenizer->is(TokenType::EXPRESSION))
        {
            $retVal->setAlias($this->parseAlias());
        }

        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\FromPart
     */
    private function parseFrom()
    {
        $this->tokenizer->expectKeyword('FROM');

        return $this->parseFromField();
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\FromPart
     */
    private function parseFromField()
    {
        $retVal = new AST\FromPart($this->tokenizer->getTokenValue());

        $this->tokenizer->expect(TokenType::EXPRESSION);

        // HAS ALIAS
        if($this->tokenizer->is(TokenType::EXPRESSION))
        {
            $retVal->setAlias($this->parseAlias());
        }
        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\Alias
     */
    private function parseAlias()
    {
        if($this->tokenizer->isTokenValue('as'))
        {
            $this->tokenizer->expect(TokenType::EXPRESSION);
        }

        $alias = $this->tokenizer->getTokenValue();

        $this->tokenizer->expect(TokenType::EXPRESSION);

        return new AST\Alias($alias);
    }

    /**
     * CONDITIONEXPR ::= ANDEXPR | OREXPR | NOTEXPR | SIMPLEEXPR
     * ANDEXPR ::= 'AND' SIMPLEEXPR
     * OREXPR ::= 'OR' SIMPLEEXPR
     * NOTEXPR ::= 'NOT' SIMPLEEXPR
     * SIMPLEEXPR ::= '(' CONDITIONEXPR ')' | FIELDEXPR | SETEXPR
     * FIELDEXPR ::= NAME OPERATOR VALUE
     * SETEXPR ::= ( NAME ('includes' | 'excludes' | 'in' | 'not' 'in') '(' VALUE (',' VALUE)* ')'  | QUERY)
     * VALUE ::= STRING_LITERAL | NUMBER | DATE | DATETIME | NULL | TRUE | FALSE | DATEFORMULA
     * OPERATOR ::= '=' | '!=' | '<' | '<=' | '>' | '>=' | 'like'
     * LOGICALOPERATOR ::= 'AND' | 'OR ' | 'NOT'
     * DATEFORMULA ::= TODAY | TOMORROW | LAST_WEEK | THIS_WEEK | NEXT_WEEK | THIS_MONTH
     *   | LAST_MONTH | NEXT_MONTH | LAST_90_DAYS | NEXT_90_DAYS | LAST_N_DAYS ':' NUMBER
     *   | NEXT_N_DAYS ':' NUMBER
     *
     * @return WherePart
     */
    private function parseWhere()
    {
        $this->tokenizer->expectKeyword('where');

        return new AST\WherePart($this->parseWhereLogicalGroup());
    }

    /**
     * // A <OP> 'B'
     * // A <OP> 1214
     * // A <OP> 2011-02-17
     * // A <OP> 2011-02-17
     * // IN[]()
     * // NOT IN[ ]()
     * // includes, excludes
     *
     * @return LogicalGroup
     */
    private function parseWhereLogicalGroup()
    {
        $retVal = new AST\LogicalGroup();

        $retVal->addAll($this->parseWhereConditions());

        return $retVal;
    }

    /**
     * @return array<LogicalJunction>
     */
    private function parseWhereConditions()
    {
        $retVal = array();

        $precedingOperator = null;

        while(true)
        {
            $junction = new AST\LogicalJunction();

            $junction->setOperator($precedingOperator);

            // NOT
            if(
                $this->tokenizer->is(TokenType::EXPRESSION) &&
                $this->tokenizer->isTokenValue('not')) {
                $junction->setIsNot(true);

                $this->tokenizer->readNextToken();
            }

            // COND AUF
            if($this->tokenizer->is(TokenType::LEFT_PAREN))
            {
                $this->tokenizer->readNextToken();

                // RECURSE ... returns LogicalGroup
                $junction->setCondition($this->parseWhereLogicalGroup());

                $this->tokenizer->expect(TokenType::RIGHT_PAREN);
            }

            // a=b,
            // dateFunction(a) = b
            // dateFunction(convertTimezone(a)) <= b
            // a=b
            // a IN|INCLUDES|EXCLUDES (a,b,c)
            // NOT a IN|INCLUDES|EXCLUDES (a,b,c)
            // a NOT IN (a,b,c)
            // a NOT IN(SELECT ...)
            // NOT a = b
            // NOT a IN b
            // a LIKE b
            // NOT a LIKE b
            else
            {
                // PARSE "x=y?" structure
                $junction->setCondition($this->parseSimpleWhereCondition());
            }

            $retVal[] = $junction;

            // VERKNÜPFUNG UND VERNEINUNG ...
            if($this->tokenizer->is(TokenType::EXPRESSION))
            {
                if($this->tokenizer->isTokenValue('or'))
                {
                    $precedingOperator = AST\LogicalJunction::OP_OR;

                    $this->tokenizer->readNextToken();

                    continue;
                }
                elseif($this->tokenizer->isTokenValue('and'))
                {
                    $precedingOperator = AST\LogicalJunction::OP_AND;

                    $this->tokenizer->readNextToken();

                    continue;
                }
            }
            break;
        }

        // WHERE PART
        return $retVal;
    }

    /**
     * VORSICHT:
     * Account[] accs = [SELECT Id FROM Account WHERE Name NOT IN ('hans') LIMIT 1];   // geht!
     * Account[] accs = [SELECT Id FROM Account WHERE NOT Name IN ('hans') LIMIT 1];   // geht!
     * Account[] accs = [SELECT Id FROM Account WHERE Name NOT LIKE ('hans') LIMIT 1]; // ERROR!
     * Account[] accs = [SELECT Id FROM Account WHERE NOT Name LIKE ('hans') LIMIT 1]; // geht!
     *
     * // a=b,
     * // a=b
     * // NOT a IN|INCLUDES|EXCLUDES (a,b,c)
     * // a IN|INCLUDES|EXCLUDES (a,b,c)
     * // a NOT IN (a,b,c)
     * // a NOT IN(SELECT ...)
     * // NOT a = b
     * // NOT a IN b
     * // a LIKE b
     * // NOT a LIKE b
     *
     * @throws ParseException
     * @return LogicalCondition
     */
    private function parseSimpleWhereCondition()
    {
        $retVal = new AST\LogicalCondition();

        $retVal->setLeft($this->parseWhereLeft());

        $retVal->setOperator($this->parseWhereOperator());

        $retVal->setRight($this->parseWhereRight());

        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlExpression|\Codemitte\ForceToolkit\Soql\AST\SoqlFunction
     * @throws ParseException
     */
    private function parseWhereLeft()
    {
        $retVal         = null;

        $name           = $this->tokenizer->getTokenValue();

        $uppercaseName  = strtoupper($name);
        $oldPos         = $this->tokenizer->getLinePos();
        $oldLine        = $this->tokenizer->getLine();

        $this->tokenizer->expect(TokenType::EXPRESSION);

        // DATE FUNCTION
        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            if(in_array($uppercaseName, self::$DATE_FUNCTIONS))
            {
                $retVal = new AST\SoqlFunction($name);

                $this->tokenizer->readNextToken();

                $retVal->addArgument($this->parseDateFunctionExpression());

                $this->tokenizer->expect(TokenType::RIGHT_PAREN);
            }
            else
            {
                throw new ParseException(sprintf('Unexpected function "%s"', $name), $oldLine, $oldPos, $this->tokenizer->getInput());
            }
        }
        else
        {
            $retVal = new AST\SoqlExpression($name);
        }

        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlExpression
     */
    private function parseWhereOperator()
    {
        // OPERATOR
        $operator = $this->tokenizer->getTokenValue();

        // NOT IN ...
        if('NOT' === $operator)
        {
            $this->tokenizer->readNextToken();

            $operator .= ' ' . $this->tokenizer->getTokenValue();
        }

        $this->tokenizer->readNextToken();

        return $operator;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\ComparableInterface
     */
    private function parseWhereRight()
    {
        $retVal = null;

        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            $this->tokenizer->readNextToken();

            if($this->tokenizer->isKeyword('select'))
            {
                // CREATE SUBQUERY
                $retVal = new AST\Subquery($this->parseQuery());
            }
            else
            {
                // COLLECTION
                $retVal = $this->parseCollectionValue();
            }

            $this->tokenizer->expect(TokenType::RIGHT_PAREN);
        }
        elseif($this->tokenizer->is(TokenType::COLON))
        {
            $retVal = $this->parseNamedVariable();
        }
        elseif($this->tokenizer->is(TokenType::QUESTION_MARK))
        {
            $retVal = $this->parseAnonVariable();
        }

        // EXPRESSION
        else
        {
            $retVal = $this->parsePrimitiveValue();
        }


        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\ComparableInterface|SoqlValue|SoqlValueCollection
     */
    private function parseValue()
    {
        $retVal = null;

        // IS COL
        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            $this->tokenizer->readNextToken();

            $retVal = $this->parseCollectionValue();

            $this->tokenizer->expect(TokenType::RIGHT_PAREN);
        }
        else
        {
            $retVal = $this->parsePrimitiveValue();
        }

        return $retVal;
    }

    /**
     * POINTER IS AT FIRST ENTRY OF COLLECTION (MAY BE COLLECTION ITSELF?)
     *
     * @return AST\SoqlValueCollection
     */
    private function parseCollectionValue()
    {
        $retVal = new AST\SoqlValueCollection();

        while(true)
        {
            $retVal->addValue($this->parseValue());

            // NACH NEM KOMMA GEHTS WEITER
            if($this->tokenizer->is(TokenType::COMMA))
            {
                $this->tokenizer->readNextToken();
                continue;
            }
            break;
        }

        return $retVal;
    }

    /**
     * @throws ParseException
     * @return \Codemitte\ForceToolkit\Soql\AST\ComparableInterface
     */
    private function parsePrimitiveValue()
    {
        $retVal = null;

        if($this->tokenizer->is(TokenType::DATE_LITERAL))
        {
            $retVal = new AST\SoqlDate($this->tokenizer->getTokenValue());

            $this->tokenizer->readNextToken();
        }
        elseif($this->tokenizer->is(TokenType::DATETIME_LITERAL))
        {
            $retVal = new AST\SoqlDateTime($this->tokenizer->getTokenValue());

            $this->tokenizer->readNextToken();
        }
        elseif($this->tokenizer->is(TokenType::NUMBER))
        {
            $retVal = new AST\SoqlNumber($this->tokenizer->getTokenValue());

            $this->tokenizer->readNextToken();
        }
        elseif($this->tokenizer->is(TokenType::STRING_LITERAL))
        {
            $retVal = new AST\SoqlString($this->tokenizer->getTokenValue());

            $this->tokenizer->readNextToken();
        }
        // DATE FORMULA OR DATE CONSTANT OR CURRENCY SYMBOL?
        elseif($this->tokenizer->is(TokenType::EXPRESSION))
        {
            $uppervaseVal = strtoupper($this->tokenizer->getTokenValue());

            if(self::BOOL_TRUE === $uppervaseVal)
            {
                $retVal = new AST\SoqlTrue();

                $this->tokenizer->readNextToken();
            }
            elseif(self::BOOL_FALSE === $uppervaseVal)
            {
                $retVal = new AST\SoqlFalse();

                $this->tokenizer->readNextToken();
            }
            elseif(self::NIL === $uppervaseVal)
            {
                $retVal = new AST\SoqlNull();

                $this->tokenizer->readNextToken();
            }
            elseif(in_array($this->tokenizer->getTokenValue(), self::$DATE_CONSTANTS))
            {

                $retVal = new AST\SoqlDateLiteral($this->tokenizer->getTokenValue());

                // ADVANCE ...
                $this->tokenizer->readNextToken();
            }
            elseif(in_array($this->tokenizer->getTokenValue(), self::$DATE_FORMULAS))
            {
                $retVal = $this->parseDateFormula();
            }
            // CURRENCY, LIKE USD5000
            elseif(preg_match('#^[A-Z]{3}\d+?(?:\\.\d+?)?$#', $this->tokenizer->getTokenValue(), $result))
            {
                $retVal = new AST\SoqlCurrencyLiteral($result[0]);
            }
            else
            {
                throw new ParseException(sprintf('Unexpected expression "%s"', $this->tokenizer->getTokenValue()), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
            }
        }
        else
        {
            throw new ParseException(sprintf('Unexpected token "%s" with value "%s"', $this->tokenizer->getTokenType(), $this->tokenizer->getTokenValue()), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }
        return $retVal;
    }

    /**
     * @return AST\SoqlDateLiteral
     */
    private function parseDateFormula()
    {
        $val = $this->tokenizer->getTokenValue();

        $this->tokenizer->readNextToken();

        $val .= ':';

        // ADVANCE ...
        $this->tokenizer->expect(TokenType::COLON);

        if(
            $this->tokenizer->is(TokenType::EXPRESSION) ||
            $this->tokenizer->is(TokenType::NUMBER)
        ){
            $val .= $this->tokenizer->getTokenValue();

            // ADVANCE ...
            $this->tokenizer->readNextToken();
        }
        else
        {
            // THROWS ERROR
            $this->tokenizer->expect(TokenType::NUMBER);
        }

        return new AST\SoqlDateLiteral($val);
    }

    /**
     * @todo: generalize!
     * @throws ParseException
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlFunctionArgumentInterface
     */
    private function parseDateFunctionExpression()
    {
        $retVal = null;

        // DATE_FUNCTION(FIELDNAME | convertTimezone(FIELDNAME))
        $name = $this->tokenizer->getTokenValue();

        $uppercaseName = strtoupper($this->tokenizer->getTokenValue());

        $oldLine = $this->tokenizer->getLine();
        $oldLinePos = $this->tokenizer->getLinePos();

        $this->tokenizer->expect(TokenType::EXPRESSION);

        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            if(in_array($uppercaseName, self::$ALLOWED_DATE_FUNCTION_FUNCTIONS))
            {
                $retVal = new AST\SoqlFunction($name);

                $this->tokenizer->readNextToken();

                $retVal->addArgument(new AST\SoqlExpression($this->tokenizer->getTokenValue()));

                $this->tokenizer->expect(TokenType::EXPRESSION);

                $this->tokenizer->expect(TokenType::RIGHT_PAREN);
            }
            else
            {
                throw new ParseException(sprintf('Unknown date conversion function "%s"', $name), $oldLine, $oldLinePos, $this->tokenizer->getInput());
            }
        }
        else
        {
            $retVal = new AST\SoqlExpression($name);
        }
        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\WithPart
     */
    private function parseWith()
    {
        $this->tokenizer->expectKeyword('with');

        $this->tokenizer->expectKeyword('data');

        $this->tokenizer->expectKeyword('category');

        return new AST\WithPart($this->parseWithLogicalGroup());
    }

    /**
     * @return LogicalGroup
     * @throws ParseException
     */
    private function parseWithLogicalGroup()
    {
        $retVal = new AST\LogicalGroup();

        $retVal->addAll($this->parseWithConditions());

        return $retVal;
    }

    /**
     * @return array
     * @throws ParseException
     */
    private function parseWithConditions()
    {
        $retVal = array();

        $precedingOperator = null;

        while(true)
        {
            $junction = new AST\LogicalJunction();

            $junction->setOperator($precedingOperator);

            // NEW LOGICAL GROUP
            if($this->tokenizer->is(TokenType::LEFT_PAREN))
            {
                $this->tokenizer->readNextToken();

                $junction->setCondition($this->parseWithLogicalGroup());

                $this->tokenizer->expect(TokenType::RIGHT_PAREN);
            }
            else
            {
                // RIGHT
                $junction->setCondition($condition = new AST\LogicalCondition());

                // ONLY SIMPLE EXPRESSION ALLOWED
                $condition->setLeft(new AST\SoqlExpression($this->tokenizer->getTokenValue()));

                // ADVANCE ...
                $this->tokenizer->expect(TokenType::EXPRESSION);

                // ABOVE, BELOW, AT, ABOVE_OR_BELOW
                $operator = $this->tokenizer->getTokenValue();

                $uppercaseOperator = strtoupper($operator);

                $oldLine = $this->tokenizer->getLine();
                $oldPos  = $this->tokenizer->getLinePos();

                $condition->setOperator($operator);

                $this->tokenizer->expect(TokenType::KEYWORD);

                if(in_array($uppercaseOperator, self::$DATA_CATEGORY_COMPARISON_OPERATORS))
                {
                    // (field1, field2) | field
                    $condition->setRight($this->parseWithFields());
                }
                else
                {
                    throw new ParseException(sprintf('Unexpected operator "%s"', $operator), $oldLine, $oldPos, $this->tokenizer->getInput());
                }
            }

            $retVal[] = $junction;

            // You can only use the AND logical operator. The following syntax is incorrect as OR is not supported:
            if($this->tokenizer->is(TokenType::EXPRESSION) && $this->tokenizer->isTokenValue('AND'))
            {
                $precedingOperator = AST\LogicalJunction::OP_AND;

                $this->tokenizer->readNextToken();

                continue;
            }
            break;
        }
        return $retVal;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\ComparableInterface
     */
    private function parseWithFields()
    {
        $retVal = null;

        // COLLECTION
        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            $retVal = new AST\SoqlValueCollection();

            $this->tokenizer->readNextToken();

            while(true)
            {
                $retVal->addValue($this->parseWithField());

                if($this->tokenizer->is(TokenType::COMMA))
                {
                    $this->tokenizer->readNextToken();

                    continue;
                }
                break;
            }

            $this->tokenizer->expect(TokenType::RIGHT_PAREN);
        }
        else
        {
            $retVal = $this->parseWithField();
        }
        return $retVal;
    }

    /**
     * @return AST\SoqlFieldReference
     */
    private function parseWithField()
    {
        $retVal = new AST\SoqlFieldReference($this->tokenizer->getTokenValue());

        $this->tokenizer->expect(TokenType::EXPRESSION);

        return $retVal;
    }

    /**
     * @return GroupPart
     */
    private function parseGroup()
    {
        $this->tokenizer->expectKeyword('group');

        $this->tokenizer->expectKeyword('by');

        $retVal = new AST\GroupByExpression();

        if($this->tokenizer->isKeyword('ROLLUP'))
        {
            $retVal->setIsRollup();

            $this->tokenizer->readNextToken();

            $this->tokenizer->expect(TokenType::LEFT_PAREN);
        }

        elseif($this->tokenizer->isKeyword('CUBE'))
        {
            $retVal->setIsCube();

            $this->tokenizer->readNextToken();

            $this->tokenizer->expect(TokenType::LEFT_PAREN);
        }

        $retVal->addGroupFields($this->parseGroupByExpression());

        // EXPECT LEFT PARANTHESIS IF ROLLUP OR CUBE
        if($retVal->getIsCube() || $retVal->getIsRollup())
        {
            $this->tokenizer->expect(TokenType::RIGHT_PAREN);
        }

        return $retVal;
    }

    /**
     * @return array<GroupField>
     */
    public function parseGroupByExpression()
    {
        $retVal = array();

        while(true)
        {
            $retVal[] = $this->parseGroupByField();

            try
            {
                $this->tokenizer->expect(TokenType::COMMA);
            }
            catch(TokenizerException $e)
            {
                break;
            }
        }

        return $retVal;
    }

    /**
     * fieldname | AggregateFunction:
     * AVG(fieldname), COUNT(FIELDNAME), COUNT_DISTINCT(fieldname), MIN(fieldname), MAX(fieldname), SUM(fieldname)
     * @return \Codemitte\ForceToolkit\Soql\AST\GroupableInterface
     */
    private function parseGroupByField()
    {
        $retVal = null;

        $fieldName = $this->tokenizer->getTokenValue();

        // ADVANCE
        $this->tokenizer->expect(TokenType::EXPRESSION);

        // IS (AGGREGATE?) FUNCTION?
        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            return $this->parseGroupByAggregateFunction($fieldName);
        }

        return new AST\GroupByField($fieldName);
    }

    /**
     * @param string $functionName
     * @throws ParseException
     */
    public function parseGroupByAggregateFunction($functionName)
    {
        $uppercaseName  = strtoupper($functionName);

        $oldPos = $this->tokenizer->getLinePos();
        $oldLine = $this->tokenizer->getLine();

        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        if(in_array($uppercaseName, self::$AGGREGATE_FUNCTIONS))
        {
            $field = new AST\SoqlAggregateFunction($functionName, $this->tokenizer->getTokenValue());

            $this->tokenizer->expect(TokenType::EXPRESSION);

            $this->tokenizer->expect(TokenType::RIGHT_PAREN);

            return $field;
        }
        throw new ParseException(sprintf('Unknown aggregate function "%s"', $uppercaseName), $oldLine, $oldPos, $this->tokenizer->getInput());
    }

    /**
     * @return AST\HavingPart
     */
    private function parseHaving()
    {
        $this->tokenizer->expectKeyword('having');

        return new AST\HavingPart($this->parseHavingLogicalGroup());
    }

    /**
     * @return LogicalGroup
     */
    private function parseHavingLogicalGroup()
    {
        $retVal = new AST\LogicalGroup();

        $retVal->addAll($this->parseHavingConditions());

        return $retVal;
    }

    /**
     * @return array<LogicalJunction>
     */
    private function parseHavingConditions()
    {
        $retVal = array();

        $precedingOperator = null;

        while(true)
        {
            $junction = new AST\LogicalJunction();

            $junction->setIsNot($this->parseHavingNot());

            $junction->setOperator($precedingOperator);

            // COND AUF
            if($this->tokenizer->is(TokenType::LEFT_PAREN))
            {
                $this->tokenizer->readNextToken();

                // RECURSE ...
                $junction->setCondition($this->parseHavingLogicalGroup());

                $this->tokenizer->expect(TokenType::RIGHT_PAREN);
            }
            else
            {
                $condition = new AST\LogicalCondition();

                $condition->setLeft($this->parseHavingLeft());

                $condition->setOperator($this->parseHavingOperator());

                $condition->setRight($this->parseHavingRight());

                $junction->setCondition($condition);
            }

            $retVal[] = $junction;

            // AND, OR
            if($this->tokenizer->is(TokenType::EXPRESSION))
            {
                if($this->tokenizer->isTokenValue('and'))
                {
                    $precedingOperator = AST\LogicalJunction::OP_AND;

                    $this->tokenizer->readNextToken();

                    continue;
                }
                elseif($this->tokenizer->isTokenValue('or'))
                {
                    $precedingOperator = AST\LogicalJunction::OP_OR;

                    $this->tokenizer->readNextToken();

                    continue;
                }
            }
            break;
        }
        return $retVal;
    }

    /**
     * @return bool: True if NOT is in statement, otherwise false
     */
    private function parseHavingNot()
    {
        if($this->tokenizer->is(TokenType::EXPRESSION) && $this->tokenizer->isTokenValue('not'))
        {
            $this->tokenizer->expect(TokenType::EXPRESSION);

            return true;
        }
        return false;
    }

    /**
     * @throws ParseException
     * @return AST\SoqlFunction
     */
    private function parseHavingLeft()
    {
        $name = $this->tokenizer->getTokenValue();

        $retVal = new AST\SoqlFunction($name);

        $uppercaseName = strtoupper($this->tokenizer->getTokenValue());

        $oldLine = $this->tokenizer->getLine();
        $oldLinePos = $this->tokenizer->getLinePos();

        // MOVE ON
        $this->tokenizer->expect(TokenType::EXPRESSION);

        // MUST BE AGGREGATE FUNCTION
        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        if(in_array($uppercaseName, self::$AGGREGATE_FUNCTIONS))
        {
            $arg = $this->tokenizer->getTokenValue();

            $retVal->addArgument(new AST\SoqlExpression($arg));

            $this->tokenizer->expect(TokenType::EXPRESSION);

            $this->tokenizer->expect(TokenType::RIGHT_PAREN);
        }
        else
        {
            throw new ParseException('Only aggregate functions allowed', $oldLine, $oldLinePos, $this->tokenizer->getInput());
        }

        return $retVal;
    }

    /**
     * @throws ParseException
     * @return AST\SoqlExpression
     */
    private function parseHavingOperator()
    {
        $retVal = null;

        $operator = $this->tokenizer->getTokenValue();

        if(in_array($this->tokenizer->getTokenType(), array(
            TokenType::OP_EQ,
            TokenType::OP_GT,
            TokenType::OP_GTE,
            TokenType::OP_LT,
            TokenType::OP_LTE,
            TokenType::OP_NE
        )))  {
            $this->tokenizer->readNextToken();
        }
        else
        {
            throw new ParseException(sprintf('Unexpected "%s"', $operator), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }
        return $operator;
    }

    /**
     * @return \Codemitte\ForceToolkit\Soql\AST\ComparableInterface
     */
    private function parseHavingRight()
    {
        $retVal = null;

        if($this->tokenizer->is(TokenType::COLON))
        {
            $retVal = $this->parseNamedVariable();
        }
        elseif($this->tokenizer->is(TokenType::QUESTION_MARK))
        {
            $retVal = $this->parseAnonVariable();
        }

        // EXPRESSION
        else
        {
            $retVal = $this->parsePrimitiveValue();
        }
        return $retVal;
    }

    /**
     * @return AST\OrderPart
     */
    private function parseOrder()
    {
        $this->tokenizer->expectKeyword('order');

        $this->tokenizer->skipWhitespace();

        $this->tokenizer->expectKeyword('by');

        $retVal = new AST\OrderPart();

        $retVal->addOrderFields($this->parseOrderByExpression());

        return $retVal;
    }

    /**
     * @return array<OrderByField>
     */
    private function parseOrderByExpression()
    {
        $retVal = array();

        while(true)
        {
            $retVal[] = $this->parseOrderByField();

            if($this->tokenizer->is(TokenType::COMMA))
            {
                $this->tokenizer->readNextToken();

                continue;
            }
            break;
        }

        return $retVal;
    }

    /**
     * ORDER BY fieldExpression ASC | DESC ? NULLS FIRST | LAST ?
     *
     * @return SortableInterface
     */
    private function  parseOrderByField()
    {
        $retVal = null;

        $fieldName = $this->tokenizer->getTokenValue();

        $oldPos = $this->tokenizer->getLinePos();
        $oldLine = $this->tokenizer->getLine();

        $uppercaseName = strtoupper($fieldName);

        $this->tokenizer->expect(TokenType::EXPRESSION);

        if($this->tokenizer->is(TokenType::LEFT_PAREN))
        {
            if(in_array($uppercaseName, self::$AGGREGATE_FUNCTIONS))
            {
                $retVal = $this->parseOrderByAggregateFunction($fieldName);
            }
            else if(in_array($uppercaseName, self::$ORDER_BY_FUNCTIONS))
            {
                $retVal = $this->parseOrderByFunction($fieldName);
            }
            else
            {
                throw new ParseException(sprintf('Unknown function "%s"', $uppercaseName), $oldLine, $oldPos, $this->tokenizer->getInput());
            }
        }
        else
        {
            $retVal = new AST\OrderByField($fieldName);
        }

        // ASC/DESC
        if($this->tokenizer->isKeyword('asc'))
        {
            $retVal->setDirection(AST\OrderByField::DIRECTION_ASC);

            $this->tokenizer->readNextToken();
        }
        elseif($this->tokenizer->isKeyword('desc'))
        {
            $retVal->setDirection(AST\OrderByField::DIRECTION_DESC);

            $this->tokenizer->readNextToken();
        }

        if($this->tokenizer->isKeyword('NULLS'))
        {
            $this->tokenizer->readNextToken();

            if($this->tokenizer->isKeyword('last'))
            {
                $retVal->setNulls(AST\OrderByField::NULLS_LAST);
            }
            elseif($this->tokenizer->isKeyword('first'))
            {
                $retVal->setNulls(AST\OrderByField::NULLS_FIRST);
            }
            else
            {
                throw new ParseException(sprintf('Unexpected "%s"', $this->tokenizer->getTokenValue()), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
            }
            $this->tokenizer->expect(TokenType::KEYWORD);
        }

        return $retVal;
    }

    /**
     * @param string $funcname
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlOrderByAggregateFunction
     * @throws ParseException
     */
    public function parseOrderByAggregateFunction($funcname)
    {
        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        $field = new AST\SoqlOrderByAggregateFunction($funcname, $this->tokenizer->getTokenValue());

        $this->tokenizer->expect(TokenType::EXPRESSION);

        $this->tokenizer->expect(TokenType::RIGHT_PAREN);

        return $field;
    }

    /**
     * @param string $funcname
     * @return \Codemitte\ForceToolkit\Soql\AST\SoqlOrderByAggregateFunction
     * @throws ParseException
     */
    public function parseOrderByFunction($funcname)
    {
        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        $field = new AST\SoqlOrderByAggregateFunction($funcname, $this->tokenizer->getTokenValue());

        $this->tokenizer->expect(TokenType::EXPRESSION);

        $this->tokenizer->expect(TokenType::RIGHT_PAREN);

        return $field;
    }

    /**
     * @return int
     */
    private function parseLimit()
    {
        $this->tokenizer->expectKeyword('limit');

        $v = $this->tokenizer->getTokenValue();

        $this->tokenizer->expect(TokenType::NUMBER);

        return $v;
    }

    /**
     * @return int
     */
    private function parseOffset()
    {
        $this->tokenizer->expectKeyword('offset');

        $v = $this->tokenizer->getTokenValue();

        $this->tokenizer->expect(TokenType::NUMBER);

        return $v;
    }

    /**
     * getNamedParameter()
     *
     * @throws ParseException
     *
     * @return AST\NamedVariable
     */
    private function parseNamedVariable()
    {
        $this->tokenizer->expect(TokenType::COLON);

        $name = $this->tokenizer->getTokenValue();

        $retVal = new AST\NamedVariable($name);

        $this->tokenizer->expect(TokenType::EXPRESSION);

        return $retVal;
    }

    /**
     * getIndexedParameter()
     *
     * @throws ParseException
     *
     * @return AST\AnonymousVariable
     */
    private function parseAnonVariable()
    {
        $this->tokenizer->expect(TokenType::ANON_VARIABLE);

        return new AST\AnonymousVariable($this->varIndex);
    }

    private function parseSelectAggregateFunction($functionName)
    {
        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        $argument = null;
        $retVal   = null;

        if( ! $this->tokenizer->is(TokenType::RIGHT_PAREN))
        {
            $argument = $this->tokenizer->getTokenValue();

            $this->tokenizer->expect(TokenType::EXPRESSION);
        }

        $retVal = new AST\SoqlSelectAggregateFunction($functionName, $argument);

        $this->tokenizer->expect(TokenType::RIGHT_PAREN);

        return $retVal;
    }

    private function parseSelectFunction($functionName)
    {
        $this->tokenizer->expect(TokenType::LEFT_PAREN);

        $retVal = new AST\SoqlSelectFunction($functionName, $this->tokenizer->getTokenValue());

        $this->tokenizer->expect(TokenType::EXPRESSION);

        $this->tokenizer->expect(TokenType::RIGHT_PAREN);

        return $retVal;
    }

    /**
     * TYPEOF special select syntax.
     * SELECT [...] TYPEOF fieldname WHEN type1 THEN fieldlist1 [WHEN type2 THEN fieldlist 2] [ELSE elsefieldlist] END
     * - SELECT TYPEOF [...] is only valid in outer SELECT clause
     * - NO GROUP BY [ROLLUP|CUBE] AND HAVING ALLOWED
     *
     * @return \Codemitte\ForceToolkit\Soql\AST\TypeofSelectPart
     */
    private function parseSelectTypeofExpression()
    {
        // sobject name
        $sobjectName = $this->tokenizer->getTokenValue();
        $this->tokenizer->expect(TokenType::EXPRESSION);

        // AT LEAST ONE "WHEN" keyword
        $this->tokenizer->expectKeyword('when');

        // CONDITION
        $sobjectType = $this->tokenizer->getTokenValue();
        $this->tokenizer->expect(TokenType::EXPRESSION);

        $this->tokenizer->expectKeyword('then');

        // THEN ...
        $fieldlist = $this->parseTypeofSelectFields();

        $typeofSelectPart = new AST\TypeofSelectPart();
        $typeofSelectPart->setSobjectType($sobjectName);

        $typeofSelectPart->addCondition(new AST\TypeofCondition($sobjectType, new AST\SelectPart($fieldlist)));

        while(true)
        {
            if($this->tokenizer->isKeyword('when'))
            {
                $this->tokenizer->readNextToken();

                $sobjectType = $this->tokenizer->getTokenValue();
                $this->tokenizer->expect(TokenType::EXPRESSION);

                $this->tokenizer->expectKeyword('then');

                $fieldlist = $this->parseTypeofSelectFields();

                $typeofSelectPart->addCondition($sobjectType, new AST\SelectPart($fieldlist));
            }
            elseif($this->tokenizer->isKeyword('else'))
            {
                $this->tokenizer->readNextToken();

                $fieldlist = $this->parseTypeofSelectFields();

                $typeofSelectPart->setElse(new AST\SelectPart($fieldlist));

                break; // ELSE IS THE END OF THE FAHNENSTANGE
            }
            else
            {
                break;
            }
        }
        return $typeofSelectPart;
    }

    /**
     * @return array<AST\SelectableFieldInterface>
     */
    private function parseTypeofSelectFields()
    {
        $selectFields = array();

        /* (SELECT, FUNCTION() [alias], fieldname [alias])
           SELECT COUNT() special case
           SELECT [...] TYPEOF fieldname WHEN type1 THEN fieldlist1 [WHEN type2 THEN fieldlist 2] [ELSE elsefieldlist] END
             - SELECT TYPEOF [...] is only valid in outer SELECT clause
             - NO GROUP BY [ROLLUP|CUBE] AND HAVING ALLOWED
        */
        while(true)
        {
            $selectFields[] = $this->parseTypeofSelectField();

            if($this->tokenizer->is(TokenType::COMMA))
            {
                $this->tokenizer->readNextToken();

                continue;
            }
            break;
        }
        return $selectFields;
    }

    /**
     *
     * @throws ParseException
     * @return AST\SelectableFieldInterface
     */
    private function parseTypeofSelectField()
    {
        $retVal = null;

        if($this->tokenizer->is(TokenType::EXPRESSION))
        {
            $name = $this->tokenizer->getTokenValue();

            $uppercaseName  = strtoupper($this->tokenizer->getTokenValue());

            $oldPos = $this->tokenizer->getLinePos();
            $oldLine = $this->tokenizer->getLine();

            $this->tokenizer->expect(TokenType::EXPRESSION);

            // REGULAR OR AGGREGATE FUNCTION
            if($this->tokenizer->is(TokenType::LEFT_PAREN))
            {
                if(in_array($uppercaseName, self::$AGGREGATE_FUNCTIONS))
                {
                    $retVal = new AST\SelectField($this->parseSelectAggregateFunction($name));
                }
                else if(in_array($uppercaseName, self::$SELECT_FUNCTIONS))
                {
                    $retVal = new AST\SelectField($this->parseSelectFunction($name));
                }
                else
                {
                    throw new ParseException(sprintf('Unknown function "%s"', $uppercaseName), $oldLine, $oldPos, $this->tokenizer->getInput());
                }
            }

            // PLAIN FIELDNAME
            else
            {
                $retVal = new AST\SelectField($name);
            }
        }
        else
        {
            throw new ParseException(sprintf('Unexpected token "%s", expecting fieldname or (aggregate) function', $this->tokenizer->getTokenType()), $this->tokenizer->getLine(), $this->tokenizer->getLinePos(), $this->tokenizer->getInput());
        }

        // ALIAS
        if($this->tokenizer->is(TokenType::EXPRESSION))
        {
            $retVal->setAlias($this->parseAlias());
        }

        return $retVal;
    }
}