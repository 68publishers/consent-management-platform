<?php

declare(strict_types=1);

namespace App\Web\Ui;

use App\Web\Control\Gtm\GtmControl;
use App\Web\Control\Gtm\GtmControlFactoryInterface;
use App\Web\Ui\Form\RecaptchaResolver;
use App\Web\Ui\Modal\PresenterTrait as ModalPresenterTrait;
use Nette\Application\AbortException;
use Nette\Application\Request;
use Nette\Application\Responses\ForwardResponse;
use Nette\Application\UI\Presenter as NettePresenter;
use SixtyEightPublishers\FlashMessageBundle\Bridge\Nette\Ui\PresenterTrait as FlashMessagePresenterTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;

abstract class Presenter extends NettePresenter implements TranslatorAwareInterface, ComponentAuthorizatorAwareInterface
{
    use TranslatorAwareTrait;
    use RedrawControlTrait;
    use FlashMessagePresenterTrait;
    use ModalPresenterTrait;
    use AuthorizationTrait;

    private TranslatorLocalizerInterface $translatorLocalizer;

    private GtmControlFactoryInterface $gtmControlFactory;

    private RecaptchaResolver $recaptchaResolver;

    /**
     *@internal
     */
    public function injectBaseDependencies(TranslatorLocalizerInterface $translatorLocalizer, GtmControlFactoryInterface $gtmControlFactory, RecaptchaResolver $recaptchaResolver): void
    {
        $this->translatorLocalizer = $translatorLocalizer;
        $this->gtmControlFactory = $gtmControlFactory;
        $this->recaptchaResolver = $recaptchaResolver;
    }

    protected function beforeRender(): void
    {
        $template = $this->getTemplate();
        assert($template instanceof DefaultPresenterTemplate);

        $template->setTranslator($this->getPrefixedTranslator());

        $template->locale = $this->translatorLocalizer->getLocale();
        $template->lang = current(explode('_', $this->translatorLocalizer->getLocale()));
        $template->user = $this->getUser();
        $template->recaptchaEnabled = $this->recaptchaResolver->isEnabled();
    }

    /**
     * @throws AbortException
     */
    public function restoreRequest($key): void
    {
        $session = $this->getSession('Nette.Application/requests');

        if (!isset($session[$key]) || ($session[$key][0] !== null && (string) $session[$key][0] !== (string) $this->getUser()->getId())) {
            return;
        }

        /** @var Request $request */
        $request = clone $session[$key][1];

        unset($session[$key]);

        $request->setFlag(Request::RESTORED);

        $params = $request->getParameters();
        $params[self::FLASH_KEY] = $this->getFlashKey();

        $request->setParameters($params);
        $this->sendResponse(new ForwardResponse($request));
    }

    protected function createComponentGtm(): GtmControl
    {
        return $this->gtmControlFactory->create();
    }

    private function getFlashKey(): ?string
    {
        $flashKey = $this->getParameter(self::FLASH_KEY);

        return is_string($flashKey) && $flashKey !== ''
            ? $flashKey
            : null;
    }
}
