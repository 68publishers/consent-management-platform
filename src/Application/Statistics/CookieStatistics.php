<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class CookieStatistics
{
    private int $numberOfProviders;

    private int $numberOfCommonCookies;

    private int $numberOfPrivateCookies;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(int $numberOfProviders, int $numberOfCommonCookies, int $numberOfPrivateCookies): self
    {
        $cookieStatistics = new self();
        $cookieStatistics->numberOfProviders = $numberOfProviders;
        $cookieStatistics->numberOfCommonCookies = $numberOfCommonCookies;
        $cookieStatistics->numberOfPrivateCookies = $numberOfPrivateCookies;

        return $cookieStatistics;
    }

    public function numberOfProviders(): int
    {
        return $this->numberOfProviders;
    }

    public function numberOfCommonCookies(): int
    {
        return $this->numberOfCommonCookies;
    }

    public function numberOfPrivateCookies(): int
    {
        return $this->numberOfPrivateCookies;
    }
}
