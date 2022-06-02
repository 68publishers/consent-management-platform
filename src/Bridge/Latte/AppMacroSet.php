<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use Latte\Engine;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Macros\MacroSet;
use Latte\CompileException;

final class AppMacroSet extends MacroSet
{
	private InternalCssRenderer $internalCssRenderer;

	/**
	 * @param \Latte\Compiler $compiler
	 */
	private function __construct(Compiler $compiler)
	{
		parent::__construct($compiler);
	}

	/**
	 * @param \Latte\Compiler                       $compiler
	 * @param \App\Bridge\Latte\InternalCssRenderer $internalCssRenderer
	 *
	 * @return void
	 */
	public static function install(Compiler $compiler, InternalCssRenderer $internalCssRenderer): void
	{
		$me = new self($compiler);
		$me->internalCssRenderer = $internalCssRenderer;

		$me->addMacro('include_css', [$me, 'macroIncludeCss']);
	}

	/**
	 * @param \Latte\Engine                         $engine
	 * @param \App\Bridge\Latte\InternalCssRenderer $internalCssRenderer
	 *
	 * @return void
	 */
	public static function installOnEngine(Engine $engine, InternalCssRenderer $internalCssRenderer): void
	{
		$engine->onCompile[] = static function () use ($engine, $internalCssRenderer) {
			self::install($engine->getCompiler(), $internalCssRenderer);
		};
	}

	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 *
	 * @return string
	 * @throws \Latte\CompileException
	 */
	public function macroIncludeCss(MacroNode $node, PhpWriter $writer): string
	{
		$tokenizer = $node->tokenizer;
		$entryName = $tokenizer->fetchWord();
		$buildName = $tokenizer->fetchWord();

		if (!is_string($entryName)) {
			throw new CompileException('First argument for Latte macro {svg} is required and argument must contains path to SVG file.');
		}

		$entryName = trim($entryName, '\'"');
		$buildName = is_string($buildName) ? trim($buildName, '\'"') : NULL;

		return $writer->write('echo <<<INLINE_STYLES' . "\n" . $this->internalCssRenderer->render($entryName, $buildName) . "\n" . 'INLINE_STYLES;');
	}
}
