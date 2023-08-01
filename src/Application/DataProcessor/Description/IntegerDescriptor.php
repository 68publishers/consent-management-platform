<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class IntegerDescriptor extends AbstractTypeDescriptor
{
    protected function createType(ContextInterface $context): Type
    {
        $type = new Type('integer');

        if (true === ($context[ContextInterface::WEAK_TYPES] ?? false)) {
            $type->before(function ($value) {
                $value = $this->tryToConvertWeakNullValue($value);

                if (!is_string($value)) {
                    return $value;
                }

                $val = trim($value);

                if (true === (bool) preg_match('/^\d+$/', $val)) {
                    $value = (int) $val;
                }

                return $value;
            });
        }

        return $type;
    }
}
