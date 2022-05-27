<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Confirm;

use Nette\Utils\Html;
use App\Web\Ui\Modal\AbstractModalControl;

final class ConfirmModalControl extends AbstractModalControl
{
	private Html $title;

	private Html $question;

	/** @var callable  */
	private $callback;

	private array $args;

	private ?string $yesButtonText = NULL;

	private ?string $noButtonText = NULL;

	/**
	 * @param \Nette\Utils\Html|string $title
	 * @param \Nette\Utils\Html|string $question
	 * @param callable                 $callback
	 * @param array                    $args
	 */
	public function __construct($title, $question, callable $callback, array $args = [])
	{
		$this->title = $title instanceof Html ? $title : Html::el()->setHtml($title);
		$this->question = $question instanceof Html ? $question : Html::el()->setHtml($question);
		$this->callback = $callback;
		$this->args = $args;
	}

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function handleConfirm(array $args): void
	{
		($this->callback)(...array_values($args));
	}

	/**
	 * @param string $yesButtonText
	 *
	 * @return $this
	 */
	public function setYesButtonText(string $yesButtonText): self
	{
		$this->yesButtonText = $yesButtonText;

		return $this;
	}

	/**
	 * @param string $noButtonText
	 *
	 * @return $this
	 */
	public function setNoButtonText(string $noButtonText): self
	{
		$this->noButtonText = $noButtonText;

		return $this;
	}

	/**
	 * @return void
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->title = $this->title;
		$this->template->question = $this->question;
		$this->template->args = $this->args;
		$this->template->yesButtonText = $this->yesButtonText;
		$this->template->noButtonText = $this->noButtonText;
	}
}
