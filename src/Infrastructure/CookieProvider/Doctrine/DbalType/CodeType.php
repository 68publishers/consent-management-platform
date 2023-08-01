<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\DbalType;

use App\Domain\CookieProvider\ValueObject\Code;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\DbalType\AbstractStringValueObjectType;

final class CodeType extends AbstractStringValueObjectType
{
    protected string $valueObjectClassname = Code::class;
}
