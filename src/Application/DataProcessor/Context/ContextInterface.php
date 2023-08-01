<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Context;

use ArrayAccess;

interface ContextInterface extends ArrayAccess
{
    public const WEAK_TYPES = 'weak_types';

    /**
     * @return static
     */
    public static function default(array $array): self;

    /**
     * @return static
     */
    public static function fromArray(array $array): self;
}
