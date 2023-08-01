<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use App\Application\Crawler\CrawlerClientProvider;
use App\Web\Ui\Modal\AbstractModalControl;
use Nette\Application\BadRequestException;
use Nette\Security\User;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenariosController;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use Throwable;

final class ScenarioDetailModalControl extends AbstractModalControl
{
    private ?ScenarioResponse $scenarioResponse = null;

    private ?string $serializedScenarioConfig = null;

    private ?Throwable $responseError = null;

    public function __construct(
        private readonly string $scenarioId,
        private readonly CrawlerClientProvider $crawlerClientProvider,
        private readonly ScenarioDetailControlFactoryInterface $scenarioDetailControlFactory,
        private readonly User $user,
    ) {}

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ScenarioDetailModalTemplate);

        $template->scenarioId = $this->scenarioId;
        $template->scenarioResponse = $this->getScenarioResponse();
        $template->responseError = $this->responseError;
        $template->user = $this->user;
    }

    /**
     * @throws BadRequestException
     */
    protected function createComponentDetail(): ScenarioDetailControl
    {
        $response = $this->getScenarioResponse();

        if (null === $response) {
            $this->error(sprintf(
                'Unable to fetch response for scenario %s. %s',
                $this->scenarioId,
                null !== $this->responseError ? (string) $this->responseError : '',
            ));
        }

        return $this->scenarioDetailControlFactory->create($response->getBody(), $this->serializedScenarioConfig ?? '{}');
    }

    private function getScenarioResponse(): ?ScenarioResponse
    {
        if (null !== $this->scenarioResponse || null !== $this->responseError) {
            return $this->scenarioResponse;
        }

        try {
            $client = $this->crawlerClientProvider->get();
            $this->scenarioResponse = $client
                ->getController(ScenariosController::class)
                ->getScenario($this->scenarioId);

            $this->serializedScenarioConfig = $client
                ->getSerializer()
                ->serialize($this->scenarioResponse->getBody()->config);
        } catch (ControllerResponseExceptionInterface $e) {
            $this->scenarioResponse = null;
            $this->responseError = $e;
        } catch (Throwable $e) {
            $this->logger->error((string) $e);

            $this->scenarioResponse = null;
            $this->responseError = $e;
        }

        return $this->scenarioResponse;
    }
}
