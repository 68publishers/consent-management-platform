<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\Presenter;
use Nette\Application\BadRequestException;
use Nette\Application\Request;

final class Error4xxPresenter extends Presenter
{
    /**
     * @throws BadRequestException
     */
    public function startup(): void
    {
        parent::startup();

        if (null !== $this->getRequest() && !$this->getRequest()->isMethod(Request::FORWARD)) {
            $this->error();
        }

        $this->setLayout(false);
    }

    public function renderDefault(BadRequestException $exception): void
    {
        $template = $this->getTemplate();
        assert($template instanceof Error4xxTemplate);

        $errorCode = $exception->getCode();
        $errorCodeString = in_array($errorCode, [404, 403], true) ? (string) $errorCode : '4xx';

        $template->errorCode = $errorCode;
        $template->errorCodeString = $errorCodeString;
    }
}
