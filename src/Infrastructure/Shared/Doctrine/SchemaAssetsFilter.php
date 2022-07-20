<?php

namespace App\Infrastructure\Shared\Doctrine;

use Doctrine\DBAL\Schema\AbstractAsset;

final class SchemaAssetsFilter
{
	/**
	 * @param mixed $asset
	 *
	 * @return bool
	 */
	public function __invoke($asset): bool
	{
		$assetName = $asset instanceof AbstractAsset ? $asset->getName() : $asset;

		return 1 === preg_match('~^(?!app_)~', $assetName);
	}
}
