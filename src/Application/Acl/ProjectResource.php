<?php

declare(strict_types=1);

namespace App\Application\Acl;

final class ProjectResource extends AbstractResource
{
    public const READ = 'read';
    public const READ_ALL = 'read_all';
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const EXPORT = 'export';
}
