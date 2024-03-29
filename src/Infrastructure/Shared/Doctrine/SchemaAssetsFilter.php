<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine;

use Doctrine\DBAL\Schema\AbstractAsset;

final class SchemaAssetsFilter
{
    /**
     * @param mixed $asset
     */
    public function __invoke(AbstractAsset|string $asset): bool
    {
        $assetName = $asset instanceof AbstractAsset ? $asset->getName() : $asset;

        return 1 === preg_match('~^(?!app_)~', $assetName);
    }
}
