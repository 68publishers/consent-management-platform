<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class Assert implements TypeDescriptorPropertyInterface
{
    /** @var callable  */
    private $validator;

    private ?string $description;

    public function __construct(callable $validator, ?string $description = null)
    {
        $this->validator = $validator;
        $this->description = $description;
    }

    public function applyToType(Type $type, ContextInterface $context): Type
    {
        return $type->assert($this->validator, $this->description);
    }
}
