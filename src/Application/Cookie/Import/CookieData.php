<?php

declare(strict_types=1);

namespace App\Application\Cookie\Import;

use App\Application\DataReader\Description\Required;
use App\Application\DataReader\Description\Descriptor;
use App\Application\DataReader\AbstractDescribedObject;
use App\Application\DataReader\Description\AllowOthers;
use App\Application\DataReader\Description\DefaultValue;
use App\Application\DataReader\Description\StructureDescriptor;

final class CookieData extends AbstractDescribedObject
{
	public string $name;

	public string $category;

	public string $provider;

	public string $processingTime;

	public bool $active;

	public array $purpose;

	/**
	 * {@inheritDoc}
	 */
	protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
	{
		return $descriptor
			->withProps(new AllowOthers())
			->withDescriptor('name', Descriptor::string(new Required()))
			->withDescriptor('category', Descriptor::string(new Required()))
			->withDescriptor('provider', Descriptor::string(new Required()))
			->withDescriptor('processingTime', Descriptor::string(new Required()))
			->withDescriptor('active', Descriptor::boolean(new DefaultValue(TRUE)))
			->withDescriptor('purpose', Descriptor::arrayOf(
				Descriptor::string(),
				Descriptor::string()
			));
	}
}
