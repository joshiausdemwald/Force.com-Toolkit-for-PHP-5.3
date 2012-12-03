<?php
namespace Codemitte\ForceToolkit\Soql\Tokenizer;

interface TokenizerInterface
{
    public function setInput($input);

    public function getInput();

    public function readNextToken();

    public function expect($tokenType);

    public function skipWhitespace();

    public function is($tokenType);

    public function isTokenValue($value);

    public function expectKeyword($keyword);

    public function isKeyword($keyword);

    /**
     * Returns true if the current token is expression or keyword. Used for
     * filtering reserved names (like "GROUP") as fieldnames.
     *
     * @return bool
     */
    public function isExpressionOrKeyword();

    public function getTokenValue();

    public function getTokenType();

    public function getLine();

    public function getLinePos();
}
