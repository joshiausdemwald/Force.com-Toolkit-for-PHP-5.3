<?php
namespace Codemitte\ForceToolkit\Soql\Tokenizer;

/**
 * Notes:
 * ======
 * NON-ALIAS-NAMES:
 * ----------------
 * AND, ASC, DESC, EXCLUDES, FIRST, FROM, GROUP, HAVING, IN, INCLUDES, LAST, LIKE, LIMIT, NOT, NULL, NULLS, OR,
 * SELECT, WHERE, WITH
 *
 * ALLOWED STRING ESCAPE SEQUENCES:
 * --------------------------------
 * You can use the following escape sequences with SOQL:
 * Sequence	Meaning
 * \n or \N	New line
 * \r or \R	Carriage return
 * \t or \T	Tab
 * \b or \B	Bell
 * \f or \F	Form feed
 * \"	One double-quote character
 * \'	One single-quote character
 * \\	Backslash
 * LIKE expression only: \_	Matches a single underscore character ( _ )
 * LIKE expression only:\%	Matches a single percent sign character ( % )
 *
 * SITUATIONS WITH "WITH":
 * =======================
 * 1.) SELECT Title FROM KnowledgeArticleVersion WHERE PublishStatus='online' WITH DATA CATEGORY Geography__c ABOVE usa__c
 * 2.) SELECT Id FROM UserProfileFeed WITH UserId='005D0000001AamR' ORDER BY CreatedDate DESC, Id DESC LIMIT 20
 */
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
     * @var bool
     */
    private $skipWhitespace = true;

    /**
     * @var array
     */
    private static $rightDelimiters = array(
        '(', ')', ',', '=', '!', '<', '>', '\'', '"', '\\', ':', ';'
    );

    /**
     * @var array
     */
    private static $charMap = array(
        ',' => TokenType::COMMA,
        ':' => TokenType::COLON,
        '?' => TokenType::QUESTION_MARK,
        ')' => TokenType::RIGHT_PAREN,
        '(' => TokenType::LEFT_PAREN,
        ';' => TokenType::SEMI_COLON
    );

    /**
     * @var array
     */
    private static $keywords = array(
        'SELECT',
        'USING',
        'FROM',
        'WHERE',

        // COMPOUND: "GROUP BY"
        'GROUP',
        'BY',
        'HAVING',
        'ORDER',
        'LIMIT',
        'OFFSET',

        // COMPOUND: "WITH DATA CATEGORY"
        'WITH',
        'DATA',
        'CATEGORY',
        'AT',
        'ABOVE',
        'BELOW',
        'ABOVE_OR_BELOW',
        'ROLLUP',
        'CUBE',
        'DESC',
        'ASC',
        'NULLS',
        'FIRST',
        'LAST'
    );

    /**
     * Sets the input string, resets the tokenizer...
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
        elseif(ctype_space($c))
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

                while(null !== ($n = $this->currentChar()) && ctype_space($n) && ! in_array($n, array("\r", "\n")))
                {
                    $this->tokenValue .= $n;

                    $this->nextChar();
                }
            }

            if($this->skipWhitespace)
            {
                $this->readNextToken();
            }
        }

        // NUMBER, COULD BE
        // INT,
        // RATIONAL NUMNER (DECIMAL, CURRENCY)
        // DATE LITERAL
        // DATE TIME LITERAL
        elseif(ctype_digit($c))
        {
            // ASSERT: NUMBER
            $this->tokenType = TokenType::NUMBER;

            while(null !== ($n = $this->currentChar()) && ! ctype_space($n) && ! in_array($n, self::$rightDelimiters))
            {
                $this->nextChar();

                if( ! ctype_digit($n) && ! in_array($n, array(
                    '.', '-', 'T', 'Z', ':', '+'
                ))) {
                    throw new TokenizerException('Unexpected char "' . $n . '"', $this->line, $this->linePos, $this->input);
                }

                if('-' === $n)
                {
                    $this->tokenType = TokenType::DATE_LITERAL;
                }

                if('T' === $n)
                {
                    $this->tokenType = TokenType::DATETIME_LITERAL;
                }

                // PUSH TOKEN CHAR
                $this->tokenValue .= $n;

                // CHECK FOR COLON (INCLUDED IN DELIMITERS, SO ADVANCE IF GOT AND IS TIMESTAMP
                if(':' === $this->currentChar() && $this->tokenType === TokenType::DATETIME_LITERAL)
                {
                    $this->tokenValue .= $this->currentChar();

                    $this->nextChar();
                }
            }

            // VALIDATE DATE/DATETIME LITERALS
            if(TokenType::DATE_LITERAL === $this->getTokenType() && false === ($this->tokenValue = \DateTime::createFromFormat('Y-m-d', $this->getTokenValue())))
            {
                throw new TokenizerException('Unexpected literal format "' . $this->getTokenValue() . '"', $this->line, $this->linePos, $this->input);
            }

            // VALIDATE DATE/DATETIME LITERALS
            if(TokenType::DATETIME_LITERAL === $this->getTokenType() && false === ($this->tokenValue = \DateTime::createFromFormat(\DateTime::W3C, $this->getTokenValue())))
            {
                throw new TokenizerException('Unexpected literal format "' . $this->getTokenValue() . '"', $this->line, $this->linePos, $this->input);
            }
        }

        // STRING LITERALS
        elseif(in_array($c, array('\'', '"')))
        {
            $closed = false;

            $this->tokenType = TokenType::STRING_LITERAL;

            while(null !== ($n = $this->currentChar()))
            {
                $this->nextChar();

                $n2 = $this->currentChar();

                if('\\' === $n)
                {
                    // CHECK ALLOWED ESCAPE SEQUENCES
                    if(in_array($n2, array('n', 'M', 'r', 'R', 't', 'T', 'b', 'B', 'f', 'F', $c, '\\', '_', '%')))
                    {
                        $this->tokenValue .= '\\' . $n2;

                        $this->nextChar();
                    }
                    else
                    {
                        throw new TokenizerException(sprintf('Unrecognized escape sequence "%s"', '\\' . $n2), $this->line, $this->linePos, $this->input);
                    }
                }
                else
                {
                    $this->tokenValue .= $n;

                    if($c === $n2)
                    {
                        $closed = true;

                        $this->tokenValue .= $n2;

                        $this->nextChar();

                        break;
                    }
                }
            }

            if( ! $closed)
            {
                throw new TokenizerException('Unterminated string literal', $this->line, $this->linePos, $this->input);
            }
        }

        // ARBITRARY CHAR (paranthesis, comma separator, ...)
        elseif(isset(self::$charMap[$c]))
        {
            $this->tokenType = self::$charMap[$c];
        }

        // (MATH.) [COMPOUNT] COMPARISON OPERATORS
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

            while(null !== ($n = $this->currentChar()) && ! ctype_space($n) && ! in_array($n, self::$rightDelimiters))
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

            // SOQL KEYWORDS
            if(in_array($t, self::$keywords))
            {
                $this->tokenType = TokenType::KEYWORD;
            }

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

    public function skipWhitespace()
    {
        while($this->is(TokenType::WHITESPACE) || $this->is(TokenType::LINEBREAK))
        {
            $this->readNextToken();
        }
    }

    /**
     * @param $type
     * @throws TokenizerException
     */
    public function expect($type)
    {
        if( ! $this->is($type))
        {
            throw new TokenizerException('Unexpected token "' . $this->tokenType . '" with value "' . $this->tokenValue . '", expected "' . $type . '"', $this->line, $this->linePos, $this->input);
        }

        $this->readNextToken();
    }

    /**
     * @param $keyword
     * @throws TokenizerException
     * @return void
     */
    public function expectKeyword($keyword)
    {
        if( ! $this->is(TokenType::KEYWORD))
        {
            throw new TokenizerException('Unexpected token "' . $this->tokenType . '" with value "' . $this->tokenValue . '", expected "'  . TokenType::KEYWORD . '"', $this->line, $this->linePos, $this->input);
        }

        if( ! $this->isTokenValue($keyword))
        {
            throw new TokenizerException('Unexpected keyword "' . $this->tokenValue. '" with value "' . $this->tokenValue . '", expected "' .$keyword  . '"', $this->line, $this->linePos, $this->input);
        }

        $this->readNextToken();
    }

    /**
     * @param $tokenType
     * @internal param $keyword
     * @return bool
     */
    public function is($tokenType)
    {
        return $this->tokenType  === $tokenType;
    }

    /**
     * @param $keyword
     * @throws TokenizerException
     * @return void
     */
    public function isKeyword($keyword)
    {
        return $this->is(TokenType::KEYWORD) && $this->isTokenValue($keyword);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isTokenValue($value)
    {
        return 0 === strcasecmp($this->tokenValue, $value);
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
