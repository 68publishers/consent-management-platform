<?php

declare(strict_types=1);

namespace App\Application\Project\Import;

use App\Application\DataProcessor\AbstractDescribedObject;
use App\Application\DataProcessor\Description\AllowOthers;
use App\Application\DataProcessor\Description\DefaultValue;
use App\Application\DataProcessor\Description\Descriptor;
use App\Application\DataProcessor\Description\Nullable;
use App\Application\DataProcessor\Description\Required;
use App\Application\DataProcessor\Description\StructureDescriptor;

final class ProjectData extends AbstractDescribedObject
{
    public string $name;

    public string $code;

    public string $domain;

    public string $color;

    public string $description;

    public bool $active;

    /** @var array<string> */
    public array $locales = [];

    public ?string $defaultLocale = null;

    /** @var array<string> */
    public array $environments = [];

    protected static function doDescribe(StructureDescriptor $descriptor): StructureDescriptor
    {
        return $descriptor
            ->withProps(new AllowOthers())
            ->withDescriptor('name', Descriptor::string(new Required()))
            ->withDescriptor('code', Descriptor::string(new Required()))
            ->withDescriptor('domain', Descriptor::string(new Required()))
            ->withDescriptor('color', Descriptor::string(new Required()))
            ->withDescriptor('description', Descriptor::string(new Required()))
            ->withDescriptor('active', Descriptor::boolean(new DefaultValue(true)))
            ->withDescriptor('locales', Descriptor::listOf(
                Descriptor::string(),
            ))
            ->withDescriptor('defaultLocale', Descriptor::string(new Nullable()))
            ->withDescriptor('environments', Descriptor::listOf(
                Descriptor::string(),
            ));
    }
}
