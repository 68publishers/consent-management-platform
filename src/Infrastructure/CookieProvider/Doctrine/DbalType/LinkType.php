<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\DbalType;

use App\Domain\CookieProvider\ValueObject\Link;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractTextValueObjectType;

final class LinkType extends AbstractTextValueObjectType
{
    protected string $valueObjectClassname = Link::class;
}
