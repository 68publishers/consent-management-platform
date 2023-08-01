<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class CreateNewCookieWithNotAcceptedCategory implements SolutionInterface
{
    private string $existingCookieId;

    private string $categoryCode;

    private string $providerName;

    public function __construct(
        string $existingCookieId,
        string $categoryCode,
        string $providerName,
    ) {
        $this->existingCookieId = $existingCookieId;
        $this->categoryCode = $categoryCode;
        $this->providerName = $providerName;
    }

    public function getType(): string
    {
        return 'create_new_cookie_with_not_accepted_category';
    }

    public function getUniqueId(): string
    {
        return md5($this->getType() . '__x__' . $this->existingCookieId);
    }

    public function getArguments(): array
    {
        return [
            'existing_cookie_id' => $this->existingCookieId,
        ];
    }

    public function getTranslatorArgs(): array
    {
        return [
            'category_code' => $this->categoryCode,
            'provider_name' => $this->providerName,
        ];
    }
}
