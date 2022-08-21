<?php

declare(strict_types=1);

namespace App\Application\CookieProvider\Import;

use App\Application\DataReader\Description\Required;
use App\Application\DataReader\Description\Descriptor;
use App\Application\DataReader\AbstractDescribedObject;
use App\Application\DataReader\Description\AllowOthers;
use App\Application\DataReader\Description\DefaultValue;
use App\Application\DataReader\Description\StructureDescriptor;

final class CookieProviderData extends AbstractDescribedObject
{
	public string $code;

	public string $name;

	public string $type;

	public string $link;

	public bool $active;

	public array $projects;

	public array $purpose;

	/**
	 * {@inheritDoc}
	 */
	protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
	{
		return $descriptor
			->withProps(new AllowOthers())
			->withDescriptor('code', Descriptor::string(new Required()))
			->withDescriptor('name', Descriptor::string(new Required()))
			->withDescriptor('type', Descriptor::string(new Required()))
			->withDescriptor('link', Descriptor::string(new Required()))
			->withDescriptor('active', Descriptor::boolean(new DefaultValue(TRUE)))
			->withDescriptor('projects', Descriptor::listOf(
				Descriptor::string(),
			))
			->withDescriptor('purpose', Descriptor::arrayOf(
				Descriptor::string(),
				Descriptor::string()
			));
	}
}
