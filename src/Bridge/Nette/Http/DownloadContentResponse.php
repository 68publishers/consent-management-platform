<?php

declare(strict_types=1);

namespace App\Bridge\Nette\Http;

use Nette\Application\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

final class DownloadContentResponse implements Response
{
    public function __construct(
        private readonly string $content,
        private readonly string $name,
        private readonly string $contentType,
        private readonly bool $forceDownload = true,
    ) {}

    public function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->contentType);
        $httpResponse->setHeader(
            'Content-Disposition',
            ($this->forceDownload ? 'attachment' : 'inline')
            . '; filename="' . $this->name . '"'
            . '; filename*=utf-8\'\'' . rawurlencode($this->name),
        );

        $httpResponse->setHeader('Content-Length', (string) strlen($this->content));

        echo $this->content;
    }
}
