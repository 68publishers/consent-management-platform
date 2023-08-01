<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Control\Footer\FooterControl;
use App\Web\Control\Footer\FooterControlFactoryInterface;
use App\Web\Control\Localization\Event\ProfileChangedEvent;
use App\Web\Control\Localization\Event\ProfileChangeFailed;
use App\Web\Control\Localization\LocalizationControl;
use App\Web\Control\Localization\LocalizationControlFactoryInterface;
use App\Web\Ui\Presenter;
use Nette\Application\AbortException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;

#[LoggedOut]
abstract class FrontPresenter extends Presenter
{
    /** @persistent */
    public string $locale;

    private FooterControlFactoryInterface $footerControlFactory;

    private LocalizationControlFactoryInterface $localizationControlFactory;

    public function injectFrontDependencies(FooterControlFactoryInterface $footerControlFactory, LocalizationControlFactoryInterface $localizationControlFactory): void
    {
        $this->footerControlFactory = $footerControlFactory;
        $this->localizationControlFactory = $localizationControlFactory;
    }

    /**
     * @throws AbortException
     */
    protected function onForbiddenRequest(ForbiddenRequestException $exception): void
    {
        if ($exception->rule instanceof LoggedOut) {
            $this->redirect(':Admin:Dashboard:');
        }
    }

    protected function createComponentFooter(): FooterControl
    {
        return $this->footerControlFactory->create();
    }

    protected function createComponentLocalization(): LocalizationControl
    {
        $control = $this->localizationControlFactory->create();

        $control->addEventListener(ProfileChangedEvent::class, function (ProfileChangedEvent $event): void {
            $this->redirect('this', [
                'locale' => $event->profile()->locale(),
            ]);
        });

        $control->addEventListener(ProfileChangeFailed::class, function (): void {
            $this->subscribeFlashMessage(FlashMessage::error('//layout.message.locale_change_failed'));
        });

        return $control;
    }
}
