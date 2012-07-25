<?php
namespace Codemitte\Sfdc\Soql\Tokenizer;

interface TokenizerInterface
{
    public function setInput($input);

    public function getInput();

    public function readNextToken();

    public function expect($type);

    public function expectKeyword($keyword);

    public function getTokenValue();

    public function getTokenType();

    public function getLine();

    public function getLinePos();
}
