<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Confirm;

use App\Web\Ui\Modal\AbstractModalControl;
use Nette\Utils\Html;

final class ConfirmModalControl extends AbstractModalControl
{
    private Html $title;

    private Html $question;

    /** @var callable  */
    private $callback;

    private ?string $yesButtonText = null;

    private ?string $noButtonText = null;

    public function __construct(
        Html|string $title,
        Html|string $question,
        callable $callback,
        private readonly array $args = [],
    ) {
        $this->title = $title instanceof Html ? $title : Html::el()->setHtml($title);
        $this->question = $question instanceof Html ? $question : Html::el()->setHtml($question);
        $this->callback = $callback;
    }

    public function handleConfirm(array $args): void
    {
        ($this->callback)(...array_values($args));
    }

    public function setYesButtonText(string $yesButtonText): self
    {
        $this->yesButtonText = $yesButtonText;

        return $this;
    }

    public function setNoButtonText(string $noButtonText): self
    {
        $this->noButtonText = $noButtonText;

        return $this;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ConfirmModalTemplate);

        $template->title = $this->title;
        $template->question = $this->question;
        $template->args = $this->args;
        $template->yesButtonText = $this->yesButtonText;
        $template->noButtonText = $this->noButtonText;
    }
}
