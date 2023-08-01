<?php

declare(strict_types=1);

namespace App\Application\Acl;

final class CrawlerScenariosResource extends AbstractResource
{
    public const READ = 'read';
    public const RUN = 'run';
    public const ABORT = 'abort';
}
