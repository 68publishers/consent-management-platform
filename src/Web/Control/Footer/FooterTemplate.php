<?php

declare(strict_types=1);

namespace App\Web\Control\Footer;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class FooterTemplate extends Template
{
	public GitRepositoryInterface $gitRepository;
}
