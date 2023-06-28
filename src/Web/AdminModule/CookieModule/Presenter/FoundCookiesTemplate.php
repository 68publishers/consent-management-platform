<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\Presenter\AdminTemplate;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnproblematicCookieSuggestion;

final class FoundCookiesTemplate extends AdminTemplate
{
	public ProjectView $projectView;

	/** @var array<ProjectView> */
	public array $allProjects;

	/** @var array<int, MissingCookieSuggestion> */
	public array $missingCookieSuggestions;

	/** @var array<int, UnassociatedCookieSuggestion> */
	public array $unassociatedCookieSuggestions;

	/** @var array<int, ProblematicCookieSuggestion> */
	public array $problematicCookieSuggestions;

	/** @var array<int, UnproblematicCookieSuggestion> */
	public array $unproblematicCookieSuggestions;

	/** @var array<int, IgnoredCookieSuggestion> */
	public array $ignoredCookieSuggestions;

	public int $totalNumberOfResolvableSuggestions;

	public int $totalNumberOfReadyToResolveSuggestions;
}
