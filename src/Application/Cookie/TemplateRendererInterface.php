<?php

declare(strict_types=1);

namespace App\Application\Cookie;

interface TemplateRendererInterface
{
	/**
	 * @param \App\Application\Cookie\Template $template
	 *
	 * @return string
	 */
	public function render(Template $template): string;
}
