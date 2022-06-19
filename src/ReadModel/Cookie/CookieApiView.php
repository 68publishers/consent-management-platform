<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\ValueObject\Name as CookieName;
use App\Domain\Category\ValueObject\Code as CategoryCode;
use App\Domain\Category\ValueObject\Name as CategoryName;
use App\Domain\CookieProvider\ValueObject\Link as CookieProviderLink;
use App\Domain\CookieProvider\ValueObject\Name as CookieProviderName;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use App\Domain\CookieProvider\ValueObject\ProviderType as CookieProviderType;

final class CookieApiView extends AbstractView
{
	public CookieName $cookieName;

	public CookieProviderName $cookieProviderName;

	public CookieProviderType $cookieProviderType;

	public CookieProviderLink $cookieProviderLink;

	public ?CategoryName $categoryName = NULL;

	public CategoryCode $categoryCode;

	public ProcessingTime $processingTime;

	public ?Purpose $purpose = NULL;

	public ?Locale $locale = NULL;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'name' => $this->cookieName->value(),
			'purpose' => NULL !== $this->purpose ? $this->purpose->value() : '',
			'processingTime' => $this->processingTime->print(NULL !== $this->locale ? $this->locale->value() : 'en', 'en'),
			'cookieProvider' => [
				'name' => $this->cookieProviderName->value(),
				'type' => $this->cookieProviderType->value(),
				'link' => $this->cookieProviderLink->value(),
			],
			'category' => [
				'name' => NULL !== $this->categoryName ? $this->categoryName->value() : '',
				'code' => $this->categoryCode->value(),
			],
		];
	}
}
