<?php
namespace Codemitte\Sfdc\Soql\Tokenizer;

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

        SEPARATOR           = 'SOQL_SEPARATOR',

        ANON_VARIABLE       = 'SOQL_ANON_VARIABLE',
        NAMED_VARIABLE      = 'SOQL_NAMED_VARIABLE',

        KEYWORD             = 'SOQL_KEYWORD',
        EXPRESSION          = 'SOQL_EXPRESSION',

        OP_EQ               = 'SOQL_OP_EQ',
        OP_NE               = 'SOQL_OP_NE',

        OP_LT               = 'SOQL_OP_LT',
        OP_LTE              = 'SOQL_OP_LTE',

        OP_GT               = 'SOQL_OP_GT',
        OP_GTE              = 'SOQL_OP_GTE',

        OP_NOT              = 'SOQL_OP_NOT',
        OP_AND              = 'SOQL_OP_AND',
        OP_OR               = 'SOQL_OP_OR',

        OP_LIKE             = 'SOQL_OP_LIKE',
        OP_IN               = 'SOQL_OP_IN',       // BEWARE OF "NOT IN"
        OP_NOT_IN           = 'SOQL_OP_NOT_IN',       // BEWARE OF "NOT IN"
        OP_INCLUDES         = 'SOQL_OP_INCLUDES',
        OP_EXCLUDES         = 'SOQL_OP_EXCLUDES'
    ;
}
