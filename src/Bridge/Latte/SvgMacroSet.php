<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use DOMElement;
use DOMDocument;
use Latte\Engine;
use ErrorException;
use Latte\Compiler;
use LogicException;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\MacroTokens;
use Latte\Macros\MacroSet;
use Latte\CompileException;

final class SvgMacroSet extends MacroSet
{
	private string $baseDir;

	/**
	 * @param \Latte\Compiler $compiler
	 * @param string          $baseDir
	 */
	public function __construct(Compiler $compiler, string $baseDir)
	{
		foreach (['dom', 'libxml'] as $ext) {
			if (!extension_loaded($ext)) {
				throw new LogicException(sprintf('Missing PHP extension "ext-%s".', $ext));
			}
		}

		if (!is_dir($baseDir)) {
			throw new LogicException(sprintf('Base directory "%s" does not exist.', $baseDir));
		}

		parent::__construct($compiler);

		$this->baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);
	}

	/**
	 * @param \Latte\Compiler $compiler
	 * @param string          $baseDir
	 *
	 * @return void
	 */
	public static function install(Compiler $compiler, string $baseDir): void
	{
		$me = new self($compiler, $baseDir);

		$me->addMacro('svg', [$me, 'macroSvg']);
	}

	/**
	 * @param \Latte\Engine $engine
	 * @param string        $baseDir
	 *
	 * @return void
	 */
	public static function installOnEngine(Engine $engine, string $baseDir): void
	{
		$engine->onCompile[] = static function () use ($engine, $baseDir) {
			self::install($engine->getCompiler(), $baseDir);
		};
	}

	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 *
	 * @return string
	 * @throws \Latte\CompileException
	 */
	public function macroSvg(MacroNode $node, PhpWriter $writer): string
	{
		$tokenizer = $node->tokenizer;
		$document = $this->createDomDocument($this->getFilePath($tokenizer));
		$userAttributes = $writer->formatArray();

		$tokenizer->reset();

		$mask = '
			echo "<svg";
			foreach (%0.raw + %1.var as $_key => $_value) {
				if (NULL === $_value) continue;

				echo " ";
				echo TRUE === $_value ? %escape($_key) : %escape($_key) . "=\"" . %escape($_value) . "\"";
			}
			echo ">" . %2.var . "</svg>";
		';

		return $writer->write(
			$mask,
			$userAttributes, # %0
			$this->buildDocumentAttributes($document), # %1
			$this->buildSvgContent($document) # %2
		);
	}

	/**
	 * @param \Latte\MacroTokens $tokens
	 *
	 * @return string
	 * @throws \Latte\CompileException
	 */
	private function getFilePath(MacroTokens $tokens): string
	{
		$path = $tokens->fetchWord();

		if (!is_string($path)) {
			throw new CompileException('First argument for Latte macro {svg} is required and argument must contains path to SVG file.');
		}

		$path = $this->baseDir . DIRECTORY_SEPARATOR . trim($path, '"\'' . DIRECTORY_SEPARATOR);

		if (!is_readable($path)) {
			throw new CompileException(sprintf(
				'File %s is not readable.',
				$path
			));
		}

		return $path;
	}

	/**
	 * @param string $path
	 *
	 * @return \DOMDocument
	 * @throws \Latte\CompileException
	 */
	private function createDomDocument(string $path): DOMDocument
	{
		$e = NULL;
		$document = new DOMDocument('1.0', 'UTF-8');
		$useInternalErrors = libxml_use_internal_errors(TRUE);

		$document->preserveWhiteSpace = FALSE;
		@$document->load($path, LIBXML_NOBLANKS);

		/** @var \LibXMLError $error */
		foreach (array_reverse(libxml_get_errors()) as $error) {
			$e = new ErrorException($error->message, $error->code, $error->level, $error->file, $error->line, $e);
		}

		libxml_clear_errors();
		libxml_use_internal_errors($useInternalErrors);

		if ($e instanceof ErrorException) {
			throw new CompileException(sprintf('Failed to load SVG from path "%s".', $path), 0, $e);
		}

		if ('svg' !== strtolower($document->documentElement->nodeName)) {
			throw new CompileException('Malformed SVG Document, root node must be <svg>');
		}

		return $document;
	}

	/**
	 * @param \DOMDocument $document
	 *
	 * @return array
	 */
	private function buildDocumentAttributes(DOMDocument $document): array
	{
		$documentAttributes = ['xmlns' => $document->documentElement->namespaceURI];
		$documentAttributes += array_column(iterator_to_array($document->documentElement->attributes), 'value', 'name');

		return $documentAttributes;
	}

	/**
	 * @param \DOMDocument $document
	 *
	 * @return string
	 */
	private function buildSvgContent(DOMDocument $document): string
	{
		return implode('', array_map(static function (DOMElement $element) use ($document) {
			return $document->saveXML($element);
		}, iterator_to_array($document->documentElement->childNodes)));
	}
}
