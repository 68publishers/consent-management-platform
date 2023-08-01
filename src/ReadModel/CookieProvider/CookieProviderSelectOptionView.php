<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieProviderSelectOptionView extends AbstractView
{
    public CookieProviderId $id;

    public Name $name;

    public Code $code;

    public bool $private;

    public function toOption(): array
    {
        return [
            $this->id->toString() => $this->name->value(),
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'code' => $this->code->value(),
            'name' => $this->name->value(),
        ];
    }
}
