<?php

declare(strict_types=1);

namespace App\Application\Mail;

final class Message
{
	private string $templateFile;

	private ?string $locale;

	private array $arguments = [];

	private ?string $subject = NULL;

	private ?Address $from = NULL;

	/** @var \App\Application\Mail\Address[]  */
	private array $to = [];

	/** @var \App\Application\Mail\Address[]  */
	private array $bcc = [];

	/** @var \App\Application\Mail\Address[]  */
	private array $cc = [];

	/** @var \App\Application\Mail\Address[]  */
	private array $replyTo = [];

	/** @var \App\Application\Mail\Attachment[]  */
	private array $attachments = [];

	private function __construct()
	{
	}

	/**
	 * @param string      $templateFile
	 * @param string|NULL $locale
	 *
	 * @return static
	 */
	public static function create(string $templateFile, ?string $locale = NULL): self
	{
		$message = new self();
		$message->templateFile = $templateFile;
		$message->locale = $locale;

		return $message;
	}

	/**
	 * @param array $arguments
	 *
	 * @return $this
	 */
	public function withArguments(array $arguments): self
	{
		$message = clone $this;
		$message->arguments = array_merge($message->arguments, $arguments);

		return $message;
	}

	/**
	 * @param string $subject
	 *
	 * @return $this
	 */
	public function withSubject(string $subject): self
	{
		$message = clone $this;
		$message->subject = $subject;

		return $message;
	}

	/**
	 * @param \App\Application\Mail\Address $from
	 *
	 * @return $this
	 */
	public function withFrom(Address $from): self
	{
		$message = clone $this;
		$message->from = $from;

		return $message;
	}

	/**
	 * @param \App\Application\Mail\Address $to
	 *
	 * @return $this
	 */
	public function withTo(Address $to): self
	{
		$message = clone $this;
		$message->to[] = $to;

		return $message;
	}

	/**
	 * @param \App\Application\Mail\Address $bcc
	 *
	 * @return $this
	 */
	public function withBcc(Address $bcc): self
	{
		$message = clone $this;
		$message->bcc[] = $bcc;

		return $message;
	}

	/**
	 * @param \App\Application\Mail\Address $cc
	 *
	 * @return $this
	 */
	public function withCc(Address $cc): self
	{
		$message = clone $this;
		$message->cc[] = $cc;

		return $message;
	}

	/**
	 * @param \App\Application\Mail\Address $replyTo
	 *
	 * @return $this
	 */
	public function withReplyTo(Address $replyTo): self
	{
		$message = clone $this;
		$message->replyTo[] = $replyTo;

		return $message;
	}

	/**
	 * @param \App\Application\Mail\Attachment $attachment
	 *
	 * @return $this
	 */
	public function withAttachment(Attachment $attachment): self
	{
		$message = clone $this;
		$message->attachments[] = $attachment;

		return $message;
	}

	/**
	 * @return string
	 */
	public function templateFile(): string
	{
		return $this->templateFile;
	}

	/**
	 * @return string|NULL
	 */
	public function locale(): ?string
	{
		return $this->locale;
	}

	/**
	 * @return array
	 */
	public function arguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @return string|NULL
	 */
	public function subject(): ?string
	{
		return $this->subject;
	}

	/**
	 * @return \App\Application\Mail\Address|NULL
	 */
	public function from(): ?Address
	{
		return $this->from;
	}

	/**
	 * @return \App\Application\Mail\Address[]
	 */
	public function to(): array
	{
		return $this->to;
	}

	/**
	 * @return \App\Application\Mail\Address[]
	 */
	public function bcc(): array
	{
		return $this->bcc;
	}

	/**
	 * @return \App\Application\Mail\Address[]
	 */
	public function cc(): array
	{
		return $this->cc;
	}

	/**
	 * @return \App\Application\Mail\Address[]
	 */
	public function replyTo(): array
	{
		return $this->replyTo;
	}

	/**
	 * @return \App\Application\Mail\Attachment[]
	 */
	public function attachments(): array
	{
		return $this->attachments;
	}
}
