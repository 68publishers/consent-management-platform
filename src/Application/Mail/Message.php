<?php

declare(strict_types=1);

namespace App\Application\Mail;

final class Message
{
    private string $templateFile;

    private ?string $locale;

    private array $arguments = [];

    private ?string $subject = null;

    private ?Address $from = null;

    /** @var array<Address> */
    private array $to = [];

    /** @var array<Address> */
    private array $bcc = [];

    /** @var array<Address> */
    private array $cc = [];

    /** @var array<Address> */
    private array $replyTo = [];

    /** @var array<Attachment> */
    private array $attachments = [];

    private function __construct() {}

    public static function create(string $templateFile, ?string $locale = null): self
    {
        $message = new self();
        $message->templateFile = $templateFile;
        $message->locale = $locale;

        return $message;
    }

    public function withArguments(array $arguments): self
    {
        $message = clone $this;
        $message->arguments = array_merge($message->arguments, $arguments);

        return $message;
    }

    public function withSubject(string $subject): self
    {
        $message = clone $this;
        $message->subject = $subject;

        return $message;
    }

    public function withFrom(Address $from): self
    {
        $message = clone $this;
        $message->from = $from;

        return $message;
    }

    public function withTo(Address $to): self
    {
        $message = clone $this;
        $message->to[] = $to;

        return $message;
    }

    public function withBcc(Address $bcc): self
    {
        $message = clone $this;
        $message->bcc[] = $bcc;

        return $message;
    }

    public function withCc(Address $cc): self
    {
        $message = clone $this;
        $message->cc[] = $cc;

        return $message;
    }

    public function withReplyTo(Address $replyTo): self
    {
        $message = clone $this;
        $message->replyTo[] = $replyTo;

        return $message;
    }

    public function withAttachment(Attachment $attachment): self
    {
        $message = clone $this;
        $message->attachments[] = $attachment;

        return $message;
    }

    public function templateFile(): string
    {
        return $this->templateFile;
    }

    public function locale(): ?string
    {
        return $this->locale;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function subject(): ?string
    {
        return $this->subject;
    }

    public function from(): ?Address
    {
        return $this->from;
    }

    /**
     * @return array<Address>
     */
    public function to(): array
    {
        return $this->to;
    }

    /**
     * @return array<Address>
     */
    public function bcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return array<Address>
     */
    public function cc(): array
    {
        return $this->cc;
    }

    /**
     * @return array<Address>
     */
    public function replyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * @return array<Attachment>
     */
    public function attachments(): array
    {
        return $this->attachments;
    }
}
