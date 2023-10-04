<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

final class IntegrationTemplate extends SelectedProjectTemplate
{
    public string $appHost;

    /** @var array<string, array<string, mixed>> */
    public array $environments;
}
