<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use Nette\Http;
use Nette\SmartObject;
use Psr\Log\LoggerInterface;
use Nette\Application\Helpers;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses;
use Nette\Application\IPresenter;
use Nette\Application\BadRequestException;

final class ErrorPresenter implements IPresenter
{
	use SmartObject;

	private LoggerInterface $logger;

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @param \Nette\Application\Request $request
	 *
	 * @return \Nette\Application\Response
	 */
	public function run(Request $request): Response
	{
		$e = $request->getParameter('exception');

		if ($e instanceof BadRequestException) {
			[$module, , $sep] = Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';

			return new Responses\ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->error((string) $e);

		return new Responses\CallbackResponse(static function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
			if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				require __DIR__ . '/templates/Error.500.phtml';
			}
		});
	}
}
