<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use Latte\Extension;
use App\Bridge\Latte\Node\IncludeCssNode;

final class LatteExtension extends Extension
{
	public function __construct(
		private readonly InternalCssRenderer $internalCssRenderer,
		private readonly NumberFormatterFilter $numberFormatterFilter,
	) {
	}

	public function getProviders(): array
	{
		return [
			'internalCssRenderer' => $this->internalCssRenderer,
		];
	}

	public function getTags(): array
	{
		return [
			'include_css' => [IncludeCssNode::class, 'create'],
		];
	}

	public function getFilters(): array
	{
		return [
			'formatNumber' => [$this->numberFormatterFilter, 'format'],
		];
	}
}
