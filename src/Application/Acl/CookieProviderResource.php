<?php

declare(strict_types=1);

namespace App\Application\Acl;

final class CookieProviderResource extends AbstractResource
{
	public const READ = 'read';
	public const CREATE = 'create';
	public const UPDATE = 'update';
	public const DELETE = 'delete';
	public const EXPORT = 'export';
}
