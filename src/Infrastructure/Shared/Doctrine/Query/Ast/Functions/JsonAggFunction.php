<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine\Query\Ast\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

final class JsonAggFunction extends FunctionNode
{
	/** @var \Doctrine\ORM\Query\AST\Node[] */
	private array $fields = [];

	private ?OrderByClause $orderBy = NULL;

	private bool $isDistinct = FALSE;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Doctrine\ORM\Query\QueryException
	 */
	public function parse(Parser $parser): void
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);

		$lexer = $parser->getLexer();

		if ($lexer->isNextToken(Lexer::T_DISTINCT)) {
			$parser->match(Lexer::T_DISTINCT);

			$this->isDistinct = TRUE;
		}

		do {
			$this->fields[] = $parser->StringPrimary();
		} while ($lexer->isNextToken(Lexer::T_COMMA) && NULL === $parser->match(Lexer::T_COMMA));

		if ($lexer->isNextToken(Lexer::T_ORDER)) {
			$this->orderBy = $parser->OrderByClause();
		}

		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSql(SqlWalker $sqlWalker): string
	{
		$fields = implode(', ', array_map(static function (Node $node) use ($sqlWalker) {
			return $sqlWalker->walkStringPrimary($node);
		}, $this->fields));

		return sprintf(
			'json_agg(%s%s%s)',
			($this->isDistinct ? 'DISTINCT ' : ''),
			1 < count($this->fields) ? '(' . $fields . ')' : $fields,
			($this->orderBy ? $sqlWalker->walkOrderByClause($this->orderBy) : '')
		);
	}
}
