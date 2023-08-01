<?php

declare(strict_types=1);

namespace App\Web\Ui;

use App\Web\Ui\Modal\ControlTrait as ModalControlTrait;
use Nette\Application\UI\Control as NetteControl;
use Nette\Security\User as NetteUser;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareInterface;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareTrait;
use SixtyEightPublishers\FlashMessageBundle\Bridge\Nette\Ui\ControlTrait as FlashMessageControlTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\TemplateResolverTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;

abstract class Control extends NetteControl implements TranslatorAwareInterface, EventDispatcherAwareInterface, LoggerAwareInterface, ComponentAuthorizatorAwareInterface
{
    use TranslatorAwareTrait;
    use RedrawControlTrait;
    use EventDispatcherAwareTrait;
    use FlashMessageControlTrait;
    use LoggerAwareTrait;
    use ModalControlTrait;
    use TemplateResolverTrait;
    use AuthorizationTrait;

    private NetteUser $user;

    /**
     *@internal
     */
    public function injectUser(NetteUser $user): void
    {
        $this->user = $user;
    }

    protected function beforeRender(): void
    {
        $this->getTemplate()->setTranslator($this->getPrefixedTranslator());
    }

    public function getUser(): NetteUser
    {
        return $this->user;
    }
}
