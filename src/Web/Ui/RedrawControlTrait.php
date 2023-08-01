<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Nette\Application\AbortException;

trait RedrawControlTrait
{
    /**
     * @throws AbortException
     */
    public function redirectIfNotAjax(): void
    {
        if (!$this->presenter->isAjax()) {
            $this->redirect('this');
        }
    }

    /**
     * @throws AbortException
     */
    public function redrawOrRedirect(string|array|null $snippet = null, bool $redraw = true): void
    {
        foreach (is_array($snippet) ? $snippet : [$snippet] as $s) {
            $this->redrawControl($s, $redraw);
        }

        $this->redirectIfNotAjax();
    }
}
