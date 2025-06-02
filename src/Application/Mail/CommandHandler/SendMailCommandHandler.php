<?php

declare(strict_types=1);

namespace App\Application\Mail\CommandHandler;

use App\Application\Mail\Address;
use App\Application\Mail\Command\SendMailCommand;
use Contributte\Mailing\IMailBuilderFactory;
use Psr\Log\LoggerInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;
use SixtyEightPublishers\TranslationBridge\PrefixedTranslatorFactoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler(bus: 'command')]
final class SendMailCommandHandler implements CommandHandlerInterface
{
    private string $templatesDirectory;

    public function __construct(
        private readonly string $projectUrl,
        string $templatesDirectory,
        private readonly Address $fromAddress,
        private readonly IMailBuilderFactory $mailBuilderFactory,
        private readonly LoggerInterface $logger,
        private readonly PrefixedTranslatorFactoryInterface $prefixedTranslatorFactory,
        private readonly TranslatorLocalizerInterface $translatorLocalizer,
    ) {
        $this->templatesDirectory = rtrim($templatesDirectory, DIRECTORY_SEPARATOR);
    }

    public function __invoke(SendMailCommand $command): void
    {
        $mailBuilder = $this->mailBuilderFactory->create();
        $message = $command->message();
        $from = $message->from() ?? $this->fromAddress;

        $mailBuilder->setFrom($from->from(), $from->name());

        foreach ($message->to() as $address) {
            $mailBuilder->addTo($address->from(), $address->name());
        }

        foreach ($message->bcc() as $address) {
            $mailBuilder->addBcc($address->from(), $address->name());
        }

        foreach ($message->cc() as $address) {
            $mailBuilder->addCc($address->from(), $address->name());
        }

        foreach ($message->replyTo() as $address) {
            $mailBuilder->addReplyTo($address->from(), $address->name());
        }

        foreach ($message->attachments() as $attachment) {
            $mailBuilder->getMessage()->addAttachment($attachment->file(), $attachment->content(), $attachment->contentType());
        }

        $templateFile = $message->templateFile();
        $mailName = null;

        if (0 === strncmp($templateFile, '~', 1)) {
            $templateFile = sprintf(
                '%s/%s',
                $this->templatesDirectory,
                mb_substr($templateFile, 1),
            );
        } elseif (0 === strncmp($templateFile, 'default:', 8)) {
            $mailName = mb_substr($templateFile, 8);
            $templateFile = sprintf(
                '%s/default.latte',
                $this->templatesDirectory,
            );
        }

        if (null === $mailName) {
            $mailName = pathinfo($templateFile, PATHINFO_FILENAME);
        }

        $arguments = array_merge(
            ['_projectUrl' => $this->projectUrl],
            $message->arguments(),
        );
        $arguments = array_merge(
            $arguments,
            ['_translatorArgs' => $arguments, '_mailName' => $mailName],
        );

        if (null !== $message->locale()) {
            $originalLocale = $this->translatorLocalizer->getLocale();
            $this->translatorLocalizer->setLocale($message->locale());
        }

        $translator = $this->prefixedTranslatorFactory->create('mail.' . $mailName);

        $mailBuilder->setTemplateFile($templateFile);
        $mailBuilder->getTemplate()->setTranslator($translator);
        $mailBuilder->setParameters($arguments);

        $mailBuilder->setSubject($message->subject() ?? $translator->translate('subject', $arguments['_translatorArgs']));

        try {
            $mailBuilder->send();
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
        }

        if (isset($originalLocale)) {
            $this->translatorLocalizer->setLocale($originalLocale);
        }
    }
}
