<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Api\Internal\Controller\StatisticsController;
use App\Application\Acl\FoundCookiesResource;
use App\Application\Acl\ProjectConsentResource;
use App\Application\Acl\ProjectCookieProviderResource;
use App\Application\Acl\ProjectCookieResource;
use App\ReadModel\Project\FindUserProjectsQuery;
use App\ReadModel\Project\ProjectView;
use App\ReadModel\User\UserView;
use App\Web\Utils\Color;
use Nette\Application\UI\InvalidLinkException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;

final class DashboardPresenter extends AdminPresenter
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
        parent::__construct();
    }

    /**
     * @throws InvalidLinkException
     * @throws IdentityException
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof DashboardTemplate);

        $projects = $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));

        $template->projectsData = array_map(fn (ProjectView $project) => [
            'code' => $project->code->value(),
            'domain' => filter_var('http://' . $project->domain->value(), FILTER_VALIDATE_URL) ?: null,
            'name' => $project->name->value(),
            'color' => $project->color->value(),
            'fontColor' => Color::resolveFontColor($project->color->value()),
            'links' => [
                'consents' => $this->getUser()->isAllowed(ProjectConsentResource::class, ProjectConsentResource::READ)
                    ? $this->link(':Admin:Project:Consents:', ['project' => $project->code->value()])
                    : null,
                'providers' => $this->getUser()->isAllowed(ProjectCookieProviderResource::class, ProjectCookieProviderResource::UPDATE)
                    ? $this->link(':Admin:Project:Providers:', ['project' => $project->code->value()])
                    : null,
                'cookies' => $this->getUser()->isAllowed(ProjectCookieResource::class, ProjectCookieResource::READ)
                    ? $this->link(':Admin:Project:Cookies:', ['project' => $project->code->value()])
                    : null,
                'cookieSuggestions' => $this->getUser()->isAllowed(FoundCookiesResource::class, FoundCookiesResource::READ)
                    ? $this->link(':Admin:Cookie:FoundCookies:', ['id' => $project->id->toString()])
                    : null,
            ],
        ], $projects);

        $identity = $this->getIdentity();
        $userData = $identity->data();
        assert($userData instanceof UserView);

        $template->requestData = [
            'endpoint' => StatisticsController::ENDPOINT_PROJECTS,
            'query' => [
                'userId' => $userData->id->toString(),
                'timezone' => $userData->timezone->getName(),
                'locale' => $userData->profileLocale->value(),
            ],
        ];
    }
}
