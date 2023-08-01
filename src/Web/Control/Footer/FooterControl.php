<?php

declare(strict_types=1);

namespace App\Web\Control\Footer;

use App\Web\Ui\Control;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class FooterControl extends Control
{
    private GitRepositoryInterface $gitRepository;

    public function __construct(GitRepositoryInterface $gitRepository)
    {
        $this->gitRepository = $gitRepository;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof FooterTemplate);

        $template->gitRepository = $this->gitRepository;
    }
}
