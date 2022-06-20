<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\ValueObject\Name as CookieName;
use App\Domain\Category\ValueObject\Code as CategoryCode;
use App\Domain\Category\ValueObject\Name as CategoryName;
use App\Domain\Cookie\ValueObject\Purpose as CookiePurpose;
use App\Domain\CookieProvider\ValueObject\Code as CookieProviderCode;
use App\Domain\CookieProvider\ValueObject\Link as CookieProviderLink;
use App\Domain\CookieProvider\ValueObject\Name as CookieProviderName;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use App\Domain\CookieProvider\ValueObject\Purpose as CookieProviderPurpose;
use App\Domain\CookieProvider\ValueObject\ProviderType as CookieProviderType;

final class CookieApiView extends AbstractView
{
	public CookieName $cookieName;

	public CookieProviderCode $cookieProviderCode;

	public CookieProviderName $cookieProviderName;

	public CookieProviderType $cookieProviderType;

	public CookieProviderLink $cookieProviderLink;

	public CookieProviderPurpose $cookieProviderPurpose;

	public CategoryName $categoryName;

	public CategoryCode $categoryCode;

	public ProcessingTime $processingTime;

	public CookiePurpose $cookiePurpose;

	public Locale $locale;

	/**
	 * @return array
	 */
	public function serializeCookieProvider(): array
	{
		return [
			'code' => $this->cookieProviderCode->value(),
			'name' => $this->cookieProviderName->value(),
			'type' => $this->cookieProviderType->value(),
			'link' => $this->cookieProviderLink->value(),
			'purpose' => $this->cookieProviderPurpose->value(),
		];
	}

	/**
	 * @param string|NULL $locale
	 *
	 * @return array
	 */
	public function serializeCookie(?string $locale = NULL): array
	{
		return [
			'name' => $this->cookieName->value(),
			'purpose' => $this->cookiePurpose->value() ,
			'processingTime' => $this->processingTime->print($locale, 'en'),
			'cookieProvider' => [
				'code' => $this->cookieProviderCode->value(),
				'name' => $this->cookieProviderName->value(),
				'type' => $this->cookieProviderType->value(),
				'link' => $this->cookieProviderLink->value(),
			],
			'category' => [
				'name' => $this->categoryName->value(),
				'code' => $this->categoryCode->value(),
			],
		];
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return $this->serializeCookie('en');
	}
}
