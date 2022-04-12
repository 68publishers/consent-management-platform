<?php

declare(strict_types=1);

namespace App\Web\Ui;

trait RedrawControlTrait
{
	/**
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function redirectIfNotAjax(): void
	{
		if (!$this->presenter->isAjax()) {
			$this->redirect('this');
		}
	}

	/**
	 * @param string|array|NULL $snippet
	 * @param bool $redraw
	 *
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function redrawOrRedirect($snippet = NULL, bool $redraw = TRUE): void
	{
		foreach (is_array($snippet) ? $snippet : [$snippet] as $s) {
			$this->redrawControl($s, $redraw);
		}

		$this->redirectIfNotAjax();
	}
}
