<?php

declare(strict_types=1);

namespace App\Application\Cookie;

interface TemplateRendererInterface
{
    public function render(Template $template): string;
}
