<?php

namespace Dope\Doctrine\Query\MySql;

use \Doctrine\ORM\Query\AST\Functions\FunctionNode,
    \Doctrine\ORM\Query\Lexer;

class Substr extends FunctionNode
{
    // (1)
    public $string = null;
    public $pos = 0;
    public $length = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // (2)
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // (3)
        $this->string = $parser->ArithmeticPrimary(); // (4)
        $parser->match(Lexer::T_COMMA); // (5)
        $this->pos = $parser->ArithmeticPrimary(); // (6)
        $parser->match(Lexer::T_COMMA); // (7)
        $this->length = $parser->ArithmeticPrimary(); // (8)
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // (9)
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        if ($this->length) {
            return 'SUBSTR(' .
                $this->string->dispatch($sqlWalker) . ', ' .
                $this->pos->dispatch($sqlWalker) . ', ' .
                $this->length->dispatch($sqlWalker) .
            ')'; // (7)
        } else {
            return 'SUBSTR(' .
                $this->string->dispatch($sqlWalker) . ', ' .
                $this->pos->dispatch($sqlWalker) .
            ')'; // (8)
        }
    }
}