<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses;
use Nette\Http;
use Nette\SmartObject;
use Psr\Log\LoggerInterface;

final class ErrorPresenter implements IPresenter
{
    use SmartObject;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

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
