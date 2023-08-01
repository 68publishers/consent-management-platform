<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Description\Path\PathInfo;
use Nette\Schema\Schema;

interface DescriptorInterface
{
    public function schema(ContextInterface $context): Schema;

    public function pathInfo(Path $path): PathInfo;
}
