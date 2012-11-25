<?php

namespace Dope\Doctrine\Query\MySql;

use \Doctrine\ORM\Query\AST\Functions\FunctionNode,
    \Doctrine\ORM\Query\Lexer;

class Soundex extends FunctionNode
{
    // (1)
    public $string = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // (2)
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // (3)
        $this->string = $parser->ArithmeticPrimary(); // (4)
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // (5)
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'SOUNDEX(' .
            $this->string->dispatch($sqlWalker) .
        ')'; // (6)
    }
}