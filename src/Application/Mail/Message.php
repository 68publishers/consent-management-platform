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

    /** @var Address[] */
    private array $to = [];

    /** @var Address[] */
    private array $bcc = [];

    /** @var Address[] */
    private array $cc = [];

    /** @var Address[] */
    private array $replyTo = [];

    /** @var Attachment[] */
    private array $attachments = [];

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(string $templateFile, ?string $locale = null): self
    {
        $message = new self();
        $message->templateFile = $templateFile;
        $message->locale = $locale;

        return $message;
    }

    /**
     * @return $this
     */
    public function withArguments(array $arguments): self
    {
        $message = clone $this;
        $message->arguments = array_merge($message->arguments, $arguments);

        return $message;
    }

    /**
     * @return $this
     */
    public function withSubject(string $subject): self
    {
        $message = clone $this;
        $message->subject = $subject;

        return $message;
    }

    /**
     * @return $this
     */
    public function withFrom(Address $from): self
    {
        $message = clone $this;
        $message->from = $from;

        return $message;
    }

    /**
     * @return $this
     */
    public function withTo(Address $to): self
    {
        $message = clone $this;
        $message->to[] = $to;

        return $message;
    }

    /**
     * @return $this
     */
    public function withBcc(Address $bcc): self
    {
        $message = clone $this;
        $message->bcc[] = $bcc;

        return $message;
    }

    /**
     * @return $this
     */
    public function withCc(Address $cc): self
    {
        $message = clone $this;
        $message->cc[] = $cc;

        return $message;
    }

    /**
     * @return $this
     */
    public function withReplyTo(Address $replyTo): self
    {
        $message = clone $this;
        $message->replyTo[] = $replyTo;

        return $message;
    }

    /**
     * @return $this
     */
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
     * @return Address[]
     */
    public function to(): array
    {
        return $this->to;
    }

    /**
     * @return Address[]
     */
    public function bcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return Address[]
     */
    public function cc(): array
    {
        return $this->cc;
    }

    /**
     * @return Address[]
     */
    public function replyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * @return Attachment[]
     */
    public function attachments(): array
    {
        return $this->attachments;
    }
}
