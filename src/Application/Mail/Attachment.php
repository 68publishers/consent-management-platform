<?php

declare(strict_types=1);

namespace App\Application\Mail;

final class Attachment
{
    private string $file;

    private ?string $content = null;

    private ?string $contentType = null;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(string $file): self
    {
        $attachment = new self();
        $attachment->file = $file;

        return $attachment;
    }

    /**
     * @return $this
     */
    public function withContent(string $content): self
    {
        $attachment = clone $this;
        $attachment->content = $content;

        return $attachment;
    }

    /**
     * @return $this
     */
    public function withContentType(string $contentType): self
    {
        $attachment = clone $this;
        $attachment->contentType = $contentType;

        return $attachment;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function contentType(): ?string
    {
        return $this->contentType;
    }
}
