<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class BooleanDescriptor extends AbstractTypeDescriptor
{
    protected function createType(ContextInterface $context): Type
    {
        $type = new Type('boolean');

        if (true === ($context[ContextInterface::WEAK_TYPES] ?? false)) {
            $type->before(function ($value) {
                $value = $this->tryToConvertWeakNullValue($value);

                if (null === $value || is_bool($value)) {
                    return $value;
                }

                $val = $value;

                if (is_string($val)) {
                    $val = trim($val);
                }

                if (in_array($val, [1, '1', 't', 'true', 'TRUE'], true)) {
                    $value = true;
                }

                if (in_array($val, [0, '0', 'f', 'false', 'FALSE'], true)) {
                    $value = false;
                }

                return $value;
            });
        }

        return $type;
    }
}
