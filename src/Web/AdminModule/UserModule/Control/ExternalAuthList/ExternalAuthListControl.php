<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\ExternalAuthList;

use App\ReadModel\User\FindExternalAuthenticationsQuery;
use App\Web\Ui\Control;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class ExternalAuthListControl extends Control
{
    public function __construct(
        private readonly string $userId,
        private readonly QueryBusInterface $queryBus,
    ) {}

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ExternalAuthListTemplate);

        $template->externalAuths = $this->queryBus->dispatch(FindExternalAuthenticationsQuery::create(
            userId: $this->userId,
        ));
    }
}
