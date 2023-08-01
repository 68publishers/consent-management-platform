<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ApiCacheSettingsChanged extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    private ApiCache $apiCache;

    /**
     * @return static
     */
    public static function create(GlobalSettingsId $globalSettingsId, ApiCache $apiCache): self
    {
        $event = self::occur($globalSettingsId->toString(), [
            'cache_control_directives' => $apiCache->cacheControlDirectives(),
            'use_entity_tag' => $apiCache->useEntityTag(),
        ]);

        $event->globalSettingsId = $globalSettingsId;
        $event->apiCache = $apiCache;

        return $event;
    }

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    public function apiCache(): ApiCache
    {
        return $this->apiCache;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
        $this->apiCache = ApiCache::create($parameters['cache_control_directives'], $parameters['use_entity_tag']);
    }
}
