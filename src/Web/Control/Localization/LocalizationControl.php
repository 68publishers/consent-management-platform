<?php

declare(strict_types=1);

namespace App\Web\Control\Localization;

use App\Application\Localization\Profiles;
use App\Web\Control\Localization\Event\ProfileChangedEvent;
use App\Web\Control\Localization\Event\ProfileChangeFailed;
use App\Web\Ui\Control;
use InvalidArgumentException;

final class LocalizationControl extends Control
{
    private Profiles $profiles;

    public function __construct(Profiles $profiles)
    {
        $this->profiles = $profiles;
    }

    public function handleChange(string $code): void
    {
        try {
            $this->dispatchEvent(new ProfileChangedEvent($this->profiles->get($code)));
        } catch (InvalidArgumentException $e) {
            $this->dispatchEvent(new ProfileChangeFailed($e, $code));
        }
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof LocalizationTemplate);

        $template->profiles = $this->profiles;
    }
}
