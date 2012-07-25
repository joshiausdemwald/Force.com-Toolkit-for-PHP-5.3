<?php
namespace Codemitte\Sfdc\Soql\Tokenizer;

class Tokenizer implements TokenizerInterface
{
    /**
     * @var string
     */
    private $input;

    /**
     * @var int
     */
    private $pos;

    /**
     * @var int
     */
    private $line;

    /**
     * @var int
     */
    private $linePos;

    /**
     * @var string
     */
    private $tokenType;

    /**
     * @var string
     */
    private $tokenValue;

    /**
     * @var array
     */
    private static $rightDelimiters = array(
        '(', ')', ',', '=', '!', '<', '>', '\'', '"', '\\'
    );

    /**
     * @var array
     */
    private static $operatorMap = array(
        'AND' => TokenType::OP_AND,
        'OR' => TokenType::OP_OR,
        'NOT' => TokenType::OP_NOT,
        'LIKE' => TokenType::OP_LIKE,
        'INCLUDES' => TokenType::OP_INCLUDES,
        'EXCLUDES' => TokenType::OP_EXCLUDES,

        // BEWARE OF THE "NOT IN"
        'IN' => TokenType::OP_IN,
        'NOT IN' => TokenType::OP_NOT_IN
    );

    /**
     * @var array
     */
    private static $charMap = array(
        ',' => TokenType::SEPARATOR,
        ')' => TokenType::RIGHT_PAREN,
        '(' => TokenType::LEFT_PAREN,
        '?' => TokenType::ANON_VARIABLE
    );

    /**
     * @var array
     */
    private static $keywords = array(
        'SELECT',
        'FROM',
        'WHERE',
        'WITH',
        'GROUP',
        'BY',
        'HAVING',
        'ORDER',
        'LIMIT',
        'OFFSET',
        'DATA',
        'CATEGORY',
        'ABOVE',
        'ABOVE_OR_BELOW',
        'BELOW',
        'ROLLUP',
        'CUBE',
        'NULL',
        'DESC',
        'ASC',
        'NULLS',
        'FIRST',
        'LAST'
    );

    /**
     * @var array
     */
    private static $dateLiterals = array(
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
        'LAST_N_DAYS:',
        'NEXT_N_DAYS:',
        'THIS_QUARTER',
        'LAST_QUARTER',
        'NEXT_QUARTER',
        'NEXT_N_QUARTERS:',
        'LAST_N_QUARTERS:',
        'THIS_YEAR',
        'LAST_YEAR',
        'NEXT_YEAR',
        'NEXT_N_YEARS:',
        'LAST_N_YEARS:',
        'THIS_FISCAL_QUARTER',
        'LAST_FISCAL_QUARTER',
        'NEXT_FISCAL_QUARTER',
        'NEXT_N_FISCAL_​QUARTERS:',
        'LAST_N_FISCAL_​QUARTERS:',
        'THIS_FISCAL_YEAR',
        'LAST_FISCAL_YEAR',
        'NEXT_FISCAL_YEAR',
        'NEXT_N_FISCAL_​YEARS:',
        'LAST_N_FISCAL_​YEARS:'
    );

    /**
     * Sets the input string
     */
    public function setInput($input)
    {
        $this->input    = $input;

        $this->pos      = 0;

        $this->line     = 0;

        $this->linePos  = 0;

        $this->tokenType = TokenType::BOF;

        $this->tokenValue = null;

        $this->length   = strlen($this->input);
    }

    /**
     * Returns the next token and sets the tokenizer's position
     * on set forward.
     *
     * @throws TokenizerException
     * @return void
     */
    public function readNextToken()
    {
        if (TokenType::EOF === $this->getTokenType())
        {
            throw new TokenizerException("Cannot read past end of stream.", $this->line, $this->linePos, $this->input);
        }

        // GET CURRENT; THEN MOVE POINTER
        $this->tokenValue = $c = $this->nextChar();

        if(null === $c)
        {
            $this->tokenType = TokenType::EOF;
        }

        // SPACE CHARACTER?
        else if(ctype_space($c))
        {
            // IS LINEBREAK
            if(in_array($c, array("\r", "\n")))
            {
                $this->tokenType = TokenType::LINEBREAK;

                // IS "\r\n"?
                if("\r" === $c && "\n" === $this->currentChar())
                {
                    $this->tokenValue .= "\n";

                    $this->nextChar();
                }

                $this->line ++;

                $this->linePos = 0;
            }

            // IS OTHER CNTRL CHAR, LET'S ASSUME IT IS WHITESPACE
            else
            {
                $this->tokenType = TokenType::WHITESPACE;

                while(($n = $this->currentChar()) && ctype_space($n) && ! in_array($n, array("\r", "\n")))
                {
                    $this->nextChar();

                    $this->tokenValue .= $n;
                }
            }
        }

        // NUMBER, COULD BE
        // INT,
        // RATIONAL NUMNER (DECIMAL, CURRENCY)
        // DATE LITERAL
        // DATE TIME LITERAL
        elseif(ctype_digit($c))
        {
            $this->tokenType = TokenType::NUMBER;

            while(($n = $this->currentChar()) && ! ctype_space($n) && ! in_array($n, self::$rightDelimiters))
            {
                if( ! ctype_digit($n) && ! in_array($n, array(
                    '.', '-', 'T', 'Z', ':'
                ))) {
                    throw new TokenizerException('Unexpected char "' . $n . '"', $this->line, $this->linePos, $this->input);
                }

                $this->nextChar();

                if('-' === $n)
                {
                    $this->tokenType = TokenType::DATE_LITERAL;
                }

                if('T' === $n)
                {
                    $this->tokenType = TokenType::DATETIME_LITERAL;
                }

                $this->tokenValue .= $n;
            }

            // VALIDATE DATE/DATETIME LITERALS
            if(TokenType::DATE_LITERAL === $this->getTokenType() && false === \DateTime::createFromFormat('Y-m-d', $this->getTokenValue()))
            {
                throw new TokenizerException('Unexpected date literal format "' . $this->getTokenValue() . '"', $this->line, $this->linePos, $this->input);
            }

            // VALIDATE DATE/DATETIME LITERALS
            if(TokenType::DATETIME_LITERAL === $this->getTokenType() && false === \DateTime::createFromFormat(\DateTime::W3C, $this->getTokenValue()))
            {
                throw new TokenizerException('Unexpected datetime literal format "' . $this->getTokenValue() . '"', $this->line, $this->linePos, $this->input);
            }
        }

        // STRING LITERALS
        elseif(in_array($c, array('\'', '\"')))
        {
            $this->tokenType = TokenType::STRING_LITERAL;

            $prev = null;

            while(($n = $this->currentChar()))
            {
                $this->nextChar();

                $this->tokenValue .= $n;

                // NOT ESCAPED?
                if($c === $n && '\\' !== $prev)
                {
                    // ADVANCE ...
                    break;
                }

                $prev = $n;
            }
        }

        // NAMED VARIABLE
        elseif(':' === $c)
        {
            $this->tokenType = TokenType::NAMED_VARIABLE;

            while(($n = $this->currentChar()) && ! ctype_space($n) && ! in_array($n, self::$rightDelimiters))
            {
                $this->nextChar();

                $this->tokenValue .= $n;
            }
        }

        elseif(isset(self::$charMap[$c]))
        {
            $this->tokenType = self::$charMap[$c];
        }

        // (MATH.) COMPARISON OPERATORS
        elseif(in_array($c, array('<', '>', '=', '!')))
        {
            $n = $this->currentChar();

            switch($c)
            {
                case '!':
                    if($n !== '=')
                    {
                        throw new TokenizerException('Unexpected "'.$n.'"', $this->line, $this->linePos, $this->input);
                    }
                    $this->tokenValue = '!=';

                    $this->tokenType = TokenType::OP_NE;

                    $this->nextChar();

                    break;

                case '<':
                    if('=' === $n)
                    {
                        $this->tokenValue = '<=';

                        $this->tokenType = TokenType::OP_LTE;

                        $this->nextChar();
                    }
                    else
                    {
                        $this->tokenType = TokenType::OP_LT;
                    }
                    break;

                case '>':
                    if('=' === $n)
                    {
                        $this->tokenValue = '>=';

                        $this->tokenType = TokenType::OP_GTE;

                        $this->nextChar();
                    }
                    else
                    {
                        $this->tokenType = TokenType::OP_GT;
                    }
                    break;

                case '=':
                    $this->tokenType = TokenType::OP_EQ;

                    break;
            }
        }

        // KEYWORDS;
        // - DATE LITERALS ("NEXT_N_YEARS:231"),
        // - ARBRITRARY EXPRESSIONS
        //   - LOGICAL OPERATORS
        //   - COMPARISON OPERATORS
        //   - FIELDNAMES
        //   - SOQL-KEYWORDS ("SELECT")
        else
        {
            // "DYNAMIC" EXPRESSIONS, CURRENTLY ONLY SUPPORTED BY DATE LITERALS
            // EXAMPLE: WHERE dateColumn < LAST_N_MONTHS:3
            $is_dynamic = false;

            $p1 = $c;

            $p2 = '';

            while(($n = $this->currentChar()) && ! ctype_space($n) && ! in_array($n, self::$rightDelimiters))
            {
                $this->nextChar();

                if( ! $is_dynamic)
                {
                    $p1 .= $n;
                }

                else
                {
                    $p2 .= $n;
                }

                $this->tokenValue .= $n;

                if(':' === $n)
                {
                    if($is_dynamic)
                    {
                        throw new TokenizerException('Unexpected ":"', $this->line, $this->linePos, $this->input);
                    }
                    $is_dynamic = true;
                }
            }

            if($is_dynamic && ! $p2)
            {
                throw new TokenizerException('Unexpected ":"', $this->line, $this->linePos, $this->input);
            }

            $t = strtoupper($p1);

            // LOGICAL OPERATOR? (AND, OR, NOT?)
            if(isset(self::$operatorMap[$t]))
            {
                $this->tokenType = self::$operatorMap[$t];
            }

            // SOQL KEYWORDS
            elseif(in_array($t, self::$keywords))
            {
                $this->tokenType = TokenType::KEYWORD;
            }

            // DATE LITERALS
            elseif(in_array($t, self::$dateLiterals))
            {
                $this->tokenType = TokenType::DATE_LITERAL;
            }

            // SHOULD BE A FUNCTION OR COLUMN NAME. BUT BEWARE OF THE PARANTHESIS
            else
            {
                $this->tokenType = TokenType::EXPRESSION;
            }
        }
    }

    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @return string|null
     */
    public function getTokenValue()
    {
        return $this->tokenValue;
    }

    /**
     * @param $type
     * @throws TokenizerException
     */
    public function expect($type)
    {
        if($this->tokenType  != $type)
        {
            throw new TokenizerException('Unexpected token "' . $this->tokenType . '"', $this->line, $this->linePos, $this->input);
        }
        $this->readNextToken();
    }

    /**
     * @param $keyword
     * @throws TokenizerException
     */
    public function expectKeyword($keyword)
    {
        if($this->tokenType  != TokenType::KEYWORD)
        {
            throw new TokenizerException('Unexpected token "' . $this->tokenType . '"', $this->line, $this->linePos, $this->input);
        }
        if(0 !== strcasecmp($this->getTokenValue(), $keyword))
        {
            throw new TokenizerException('Expected "' . $keyword . '", got ' . $this->getTokenValue() . '"', $this->line, $this->linePos, $this->input);
        }
        $this->readNextToken();
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getLinePos()
    {
        $linePos = $this->linePos;

        if($this->tokenValue)
        {
            $linePos = $linePos - strlen($this->tokenValue);
        }

        return $linePos;
    }

    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return string|null
     */
    private function currentChar()
    {
        if( ! $this->isEOF())
        {
            return $this->input[$this->pos];
        }
        return null;
    }

    /**
     * Returns current char,
     * then moves pointer.
     *
     * @return string|null
     */
    private function nextChar()
    {
        if( ! $this->isEOF())
        {
            $this->linePos ++;

            return $this->input[$this->pos++];
        }
        return null;
    }

    /**
     * @return boolean
     */
    private function isEOF()
    {
        return $this->pos >= $this->length;
    }
}
