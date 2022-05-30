<?php

declare(strict_types=1);

namespace App\Web\SwaggerModule\Presenter;

use Nette\Application\UI\Presenter;
use App\Application\OpenApiConfiguration;
use Nette\Application\ForbiddenRequestException;

final class SwaggerPresenter extends Presenter
{
	private OpenApiConfiguration $openApiConfiguration;

	/**
	 * @param \App\Application\OpenApiConfiguration $openApiConfiguration
	 */
	public function __construct(OpenApiConfiguration $openApiConfiguration)
	{
		parent::__construct();

		$this->openApiConfiguration = $openApiConfiguration;
	}

	/**
	 * @return void
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	protected function startup(): void
	{
		parent::startup();

		if (!$this->openApiConfiguration->enabled()) {
			throw new ForbiddenRequestException('OpenApi is disabled.');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->configuration = $this->openApiConfiguration;
	}
}
