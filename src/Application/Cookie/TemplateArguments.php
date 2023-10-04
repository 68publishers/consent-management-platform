<?php

declare(strict_types=1);

namespace App\Application\Cookie;

final class TemplateArguments
{
    private array $providers;

    private array $cookies;

    private ?string $environment;

    private function __construct() {}

    /**
     * @param array<object> $providers
     * @param array<object> $cookies
     */
    public static function create(array $providers, array $cookies, ?string $environment = null): self
    {
        $arguments = new self();
        $arguments->providers = $providers;
        $arguments->cookies = $cookies;
        $arguments->environment = $environment;

        return $arguments;
    }

    /**
     * @return array<object>
     */
    public function providers(): array
    {
        return $this->providers;
    }

    /**
     * @return array<object>
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    public function environment(): ?string
    {
        return $this->environment;
    }
}
