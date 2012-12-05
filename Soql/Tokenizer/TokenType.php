<?php
namespace Codemitte\ForceToolkit\Soql\Tokenizer;

class TokenType
{
    const
        BOF                 = 'SOQL_BOF',
        EOF                 = 'SOQL_EOF',
        LINEBREAK           = 'SOQL_LINEBREAK',
        WHITESPACE          = 'SOQL_WHITESPACE',

        STRING_LITERAL      = 'SOQL_STRING_LITERAL',
        DATETIME_LITERAL    = 'SOQL_DATETIME_LITERAL',
        DATE_LITERAL        = 'SOQL_DATE_LITERAL',
        NUMBER              = 'SOQL_NUMBER',

        LEFT_PAREN          = 'SOQL_LEFT_PAREN',
        RIGHT_PAREN         = 'SOQL_RIGHT_PAREN',

        KEYWORD             = 'SOQL_KEYWORD',

        COMMA               = 'SOQL_COMMA',
        COLON               = 'SOQL_COLON',
        SEMI_COLON          = 'SOQL_SEMI_COLON',
        QUESTION_MARK       = 'SOQL_QUESTION_MARK',

        OP_EQ               = 'SOQL_OP_EQ',
        OP_NE               = 'SOQL_OP_NE',

        OP_LT               = 'SOQL_OP_LT',
        OP_LTE              = 'SOQL_OP_LTE',

        OP_GT               = 'SOQL_OP_GT',
        OP_GTE              = 'SOQL_OP_GTE',

        // ALLES ANDERE; FIELDNAMES, FUNCTIONS
        EXPRESSION          = 'SOQL_EXPRESSION',

        // +/-
        SIGN                = 'SOQL_SIGN'
    ;
}
