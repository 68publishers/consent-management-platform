<?php

declare(strict_types=1);

namespace App\Bridge\Nette\Http;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Application\Response;

final class DownloadContentResponse implements Response
{
	private string $content;

	private string $contentType;

	private string $name;

	private bool $forceDownload;

	/**
	 * @param string $content
	 * @param string $name
	 * @param string $contentType
	 * @param bool   $forceDownload
	 */
	public function __construct(string $content, string $name, string $contentType, bool $forceDownload = TRUE)
	{
		$this->content = $content;
		$this->name = $name;
		$this->contentType = $contentType;
		$this->forceDownload = $forceDownload;
	}

	/**
	 * {@inheritDoc}
	 */
	public function send(IRequest $httpRequest, IResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setHeader(
			'Content-Disposition',
			($this->forceDownload ? 'attachment' : 'inline')
			. '; filename="' . $this->name . '"'
			. '; filename*=utf-8\'\'' . rawurlencode($this->name)
		);

		$httpResponse->setHeader('Content-Length', (string) strlen($this->content));

		echo $this->content;
	}
}
