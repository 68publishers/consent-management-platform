<?php

declare(strict_types=1);

namespace App\Application\Project\Import;

use App\Application\DataReader\Description\Required;
use App\Application\DataReader\Description\Descriptor;
use App\Application\DataReader\AbstractDescribedObject;
use App\Application\DataReader\Description\DefaultValue;
use App\Application\DataReader\Description\StructureDescriptor;

final class ProjectData extends AbstractDescribedObject
{
	public string $name;

	public string $code;

	public string $color;

	public string $description;

	public bool $active;

	public array $locales;

	public string $defaultLocale;

	/**
	 * {@inheritDoc}
	 */
	protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
	{
		return $descriptor
			->withDescriptor('name', Descriptor::string(new Required()))
			->withDescriptor('code', Descriptor::string(new Required()))
			->withDescriptor('color', Descriptor::string(new Required()))
			->withDescriptor('description', Descriptor::string(new Required()))
			->withDescriptor('active', Descriptor::boolean(new DefaultValue(TRUE)))
			->withDescriptor('locales', Descriptor::listOf(
				Descriptor::string(),
			))
			->withDescriptor('defaultLocale', Descriptor::string(new Required()));
	}
}
