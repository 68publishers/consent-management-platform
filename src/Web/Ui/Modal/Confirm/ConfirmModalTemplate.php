<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Confirm;

use Nette\Utils\Html;
use Nette\Bridges\ApplicationLatte\Template;

final class ConfirmModalTemplate extends Template
{
	public Html $title;

	public Html $question;

	public array $args = [];

	public ?string $yesButtonText = NULL;

	public ?string $noButtonText = NULL;
}
