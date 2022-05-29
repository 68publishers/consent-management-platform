<?php

declare(strict_types=1);

namespace App\Web\Control\Footer;

use App\Web\Ui\Control;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class FooterControl extends Control
{
	private GitRepositoryInterface $gitRepository;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface $gitRepository
	 */
	public function __construct(GitRepositoryInterface $gitRepository)
	{
		$this->gitRepository = $gitRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->gitRepository = $this->gitRepository;
	}
}
