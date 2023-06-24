<?php

declare(strict_types=1);

namespace App\Application\Cookie\Import;

use App\Application\DataProcessor\Description\Required;
use App\Application\DataProcessor\Description\Descriptor;
use App\Application\DataProcessor\AbstractDescribedObject;
use App\Application\DataProcessor\Description\AllowOthers;
use App\Application\DataProcessor\Description\DefaultValue;
use App\Application\DataProcessor\Description\StructureDescriptor;

final class CookieData extends AbstractDescribedObject
{
	public string $name;

	public string $domain;

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
			->withDescriptor('domain', Descriptor::string(new DefaultValue('')))
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
