<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion;

final class Matcher
{
	private function __construct()
	{
	}

	/**
	 * 0 - no match
	 * 1 - matched (both name and domain)
	 * 2 - similar (matched name and cookie domain is empty, project domain not matched)
	 */
	public static function matchCookie(string $cookieName, string $cookieDomain, string $projectDomain, string $suggestedCookieName, string $suggestedCookieDomain): int
	{
		$successfulResult = 2;

		if (!empty($cookieDomain)) {
			if (!self::matchDomain($cookieDomain, $suggestedCookieDomain)) {
				return 0;
			}

			$successfulResult = 1;
		} elseif (self::matchDomain($projectDomain, $suggestedCookieDomain)) {
			$successfulResult = 1;
		}

		if (FALSE === strpos($cookieName, '*')) {
			return $cookieName === $suggestedCookieName ? $successfulResult : 0;
		}

		$regex = str_replace(
			["\*"], # wildcard chars
			['.*'], # regexp chars
			preg_quote($cookieName, '/')
		);

		return preg_match('/^'.$regex.'$/s', $suggestedCookieName) ? $successfulResult : 0;
	}

	public static function matchDomain(string $cookieDomain, string $suggestedCookieDomain): bool
	{
		# strip leading dot
		$cookieDomain = 0 === strncmp($cookieDomain, '.', 1) ? substr($cookieDomain, 1) : $cookieDomain;
		$suggestedCookieDomain = 0 === strncmp($suggestedCookieDomain, '.', 1) ? substr($suggestedCookieDomain, 1) : $suggestedCookieDomain;

		# suggested domain must ends with cookie domain, examples:
		# 1. cookieDomain = "example.com", suggestedDomain = "example.com", result = true
		# 2. cookieDomain = "example.com", suggestedDomain = "www.example.com", result = true
		# 3. cookieDomain = "example.com", suggestedDomain = "subdomain.example.com", result = true
		# 4. cookieDomain = "example.com", suggestedDomain = "youtube.com", result = false
		return $cookieDomain === substr($suggestedCookieDomain, -strlen($cookieDomain));
	}
}
