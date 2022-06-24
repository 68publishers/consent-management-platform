<?php

declare(strict_types=1);

namespace App\Application\Cookie;

final class Template
{
	public const OPTION_NO_CACHE = 'no_cache';

	private string $projectId;

	private string $template;

	private TemplateArguments $arguments;

	private array $options = [];

	private function __construct()
	{
	}

	/**
	 * @param string                                    $projectId
	 * @param string                                    $template
	 * @param \App\Application\Cookie\TemplateArguments $arguments
	 *
	 * @return static
	 */
	public static function create(string $projectId, string $template, TemplateArguments $arguments): self
	{
		$cookieTemplate = new self();
		$cookieTemplate->projectId = $projectId;
		$cookieTemplate->template = $template;
		$cookieTemplate->arguments = $arguments;

		return $cookieTemplate;
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->projectId;
	}

	/**
	 * @return string
	 */
	public function template(): string
	{
		return $this->template;
	}

	/**
	 * @return \App\Application\Cookie\TemplateArguments
	 */
	public function arguments(): TemplateArguments
	{
		return $this->arguments;
	}

	/**
	 * @return array
	 */
	public function options(): array
	{
		return $this->options;
	}

	/**
	 * @param string     $key
	 * @param mixed|NULL $default
	 *
	 * @return mixed|NULL
	 */
	public function option(string $key, $default = NULL)
	{
		return $this->options()[$key] ?? $default;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function withOption(string $key, $value): self
	{
		$cookieTemplate = clone $this;
		$cookieTemplate->options[$key] = $value;

		return $cookieTemplate;
	}
}
