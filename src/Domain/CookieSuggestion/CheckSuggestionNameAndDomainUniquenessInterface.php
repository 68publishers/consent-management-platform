<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion;

use App\Domain\CookieSuggestion\Exception\NameAndDomainUniquenessException;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use App\Domain\CookieSuggestion\ValueObject\Domain;
use App\Domain\CookieSuggestion\ValueObject\Name;
use App\Domain\Project\ValueObject\ProjectId;

interface CheckSuggestionNameAndDomainUniquenessInterface
{
    /**
     * @throws NameAndDomainUniquenessException
     */
    public function __invoke(CookieSuggestionId $cookieSuggestionId, ProjectId $projectId, Name $name, Domain $domain): void;
}
