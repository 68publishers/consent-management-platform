<?php

declare(strict_types=1);

namespace App\Application\Mail;

final class Attachment
{
	private string $file;

	private ?string $content = NULL;

	private ?string $contentType = NULL;

	private function __construct()
	{
	}

	/**
	 * @param string $file
	 *
	 * @return static
	 */
	public static function create(string $file): self
	{
		$attachment = new self();
		$attachment->file = $file;

		return $attachment;
	}

	/**
	 * @param string $content
	 *
	 * @return $this
	 */
	public function withContent(string $content): self
	{
		$attachment = clone $this;
		$attachment->content = $content;

		return $attachment;
	}

	/**
	 * @param string $contentType
	 *
	 * @return $this
	 */
	public function withContentType(string $contentType): self
	{
		$attachment = clone $this;
		$attachment->contentType = $contentType;

		return $attachment;
	}

	/**
	 * @return string
	 */
	public function file(): string
	{
		return $this->file;
	}

	/**
	 * @return string|NULL
	 */
	public function content(): ?string
	{
		return $this->content;
	}

	/**
	 * @return string|NULL
	 */
	public function contentType(): ?string
	{
		return $this->contentType;
	}
}
