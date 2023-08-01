<?php

declare(strict_types=1);

namespace App\Bridge\Latte\Node;

use Generator;
use Latte\CompileException;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

/**
 * {include_css, entryName, ...}
 */
final class IncludeCssNode extends StatementNode
{
    public ExpressionNode $entryName;

    public ArrayNode $otherArguments;

    /**
     * @throws CompileException
     */
    public static function create(Tag $tag): self
    {
        $tag->expectArguments();
        $node = new self;
        $node->entryName = $tag->parser->parseUnquotedStringOrExpression();

        $tag->parser->stream->tryConsume(',');
        $node->otherArguments = $tag->parser->parseArguments();

        return $node;
    }

    public function print(PrintContext $context): string
    {
        return $context->format(
            "echo <<<INLINE_STYLES\n{\$this->global->internalCssRenderer->render(%node, %args?)}\nINLINE_STYLES; %line;",
            $this->entryName,
            $this->otherArguments,
            $this->position,
        );
    }

    public function &getIterator(): Generator
    {
        yield $this->entryName;
        yield $this->otherArguments;
    }
}
