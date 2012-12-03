<?php
namespace Codemitte\ForceToolkit\Soql\AST\Functions;

use Codemitte\ForceToolkit\Soql\AST\Functions\Aggregate;
use Codemitte\ForceToolkit\Soql\Parser\ParseException;
use Codemitte\ForceToolkit\Soql\Tokenizer\TokenizerInterface;

class Factory
{
    /**
     * @param string $name
     * @param int $context
     * @param \Codemitte\ForceToolkit\Soql\Tokenizer\TokenizerInterface $tokenizer
     * @param array $arguments
     * @throws \Codemitte\ForceToolkit\Soql\Parser\ParseException
     * @return SoqlFunctionInterface
     */
    public static function getInstance($name, $context, TokenizerInterface $tokenizer, array $arguments = array())
    {
        $uppercaseName = strtoupper($name);

        $retVal = null;

        switch ($uppercaseName)
        {
            // AGGREGATE FUNCTIONS
            case 'SUM':
                $retVal = new Aggregate\Sum($arguments);
                break;
            case 'AVG':
                $retVal = new Aggregate\Avg($arguments);
                break;
            case 'COUNT':
                // SONDERLOCKE
                if($context === SoqlFunctionInterface::CONTEXT_SELECT && 0 === count($arguments))
                {
                    $retVal = new Count();
                }
                else
                {
                    $retVal = new Aggregate\Cnt($arguments);
                }
                break;
            case 'COUNT_DISTINCT':
                $retVal = new Aggregate\CntDistinct($arguments);
                break;
            case 'MAX':
                $retVal = new Aggregate\Max($arguments);
                break;
            case 'MIN':
                $retVal = new Aggregate\Min($arguments);
                break;

            // DATE FUNCTIONS
            case 'CALENDAR_MONTH':
                $retVal = new CalendarMonth($arguments);
                break;
            case 'CALENDAR_QUARTER':
                $retVal = new CalendarQuarter($arguments);
                break;
            case 'CALENDAR_YEAR':
                $retVal = new CalendarYear($arguments);
                break;
            case 'DAY_IN_MONTH':
                $retVal = new CalendarMonth($arguments);
                break;
            case 'DAY_IN_WEEK':
                $retVal = new DayInWeek($arguments);
                break;
            case 'DAY_IN_YEAR':
                $retVal = new DayInYear($arguments);
                break;
            case 'DAY_ONLY':
                $retVal = new DayOnly($arguments);
                break;
            case 'FISCAL_MONTH':
                $retVal = new FiscalMonth($arguments);
                break;
            case 'FISCAL_QUARTER':
                $retVal = new FiscalQuarter($arguments);
                break;
            case 'FISCAL_YEAR':
                $retVal = new FiscalYear($arguments);
                break;
            case 'HOUR_IN_DAY':
                $retVal = new HourInDay($arguments);
                break;
            case 'WEEK_IN_MONTH':
                $retVal = new WeekInMonth($arguments);
                break;
            case 'WEEK_IN_YEAR':
                $retVal = new WeekInYear($arguments);
                break;

            // SELECT FUNCTIONS
            case 'GROUPING':
                $retVal = new Grouping($arguments);
                break;
            case 'TOLABEL':
                $retVal = new ToLabel($arguments);
                break;
            case 'CONVERTCURRENCY':
                $retVal = new ConvertCurrency($arguments);
                break;

            case 'CONVERTTIMEZONE':
                $retVal = new ConvertTimezone($arguments);
                break;

            // GEOFUNCTIONS (CURRENTLY ONLY SUPPORTED IN WHERE CLAUSE)
            case 'DISTANCE':
                $retVal = new Distance($arguments);
                break;
            case 'GEOLOCATION':
                $retVal = new Geolocation($arguments);
                break;

            default:
                throw new ParseException(sprintf('Unknown function "%s" in context "%s"', $name, $context), $tokenizer->getLine(), $tokenizer->getLinePos(), $tokenizer->getInput());
        }

        if( ! ($retVal->getAllowedContext() & $context))
        {
            throw new ParseException(sprintf('Unexpected function: "%s" is not allowed in context "%s"', $name, $context), $tokenizer->getLine(), $tokenizer->getLinePos(), $tokenizer->getInput());
        }

        return $retVal;
    }
}

