<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\Query\Ast\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

/**
 * "COUNT_ROWS()"
 */
final class CountRows extends FunctionNode
{
    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'COUNT(*)';
    }

    /**
     * @throws QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
