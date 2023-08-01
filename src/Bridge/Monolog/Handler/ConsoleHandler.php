<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Handler;

use App\Bridge\Monolog\Formatter\ConsoleFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConsoleHandler extends AbstractProcessingHandler implements EventSubscriberInterface
{
    private ?OutputInterface $output;

    private array $verbosityLevelMap = [
        OutputInterface::VERBOSITY_QUIET => Logger::ERROR,
        OutputInterface::VERBOSITY_NORMAL => Logger::WARNING,
        OutputInterface::VERBOSITY_VERBOSE => Logger::NOTICE,
        OutputInterface::VERBOSITY_VERY_VERBOSE => Logger::INFO,
        OutputInterface::VERBOSITY_DEBUG => Logger::DEBUG,
    ];

    public function __construct(OutputInterface $output = null, bool $bubble = true, array $verbosityLevelMap = [])
    {
        parent::__construct(Logger::DEBUG, $bubble);

        $this->output = $output;

        if ($verbosityLevelMap) {
            $this->verbosityLevelMap = $verbosityLevelMap;
        }
    }

    public function isHandling(array $record): bool
    {
        return $this->updateLevel() && parent::isHandling($record);
    }

    public function handle(array $record): bool
    {
        return $this->updateLevel() && parent::handle($record);
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function close(): void
    {
        $this->output = null;

        parent::close();
    }

    /**
     *@internal
     */
    public function onCommand(ConsoleCommandEvent $event): void
    {
        $this->setOutput($event->getOutput());
    }

    /**
     * @internal
     */
    public function onTerminate(): void
    {
        $this->close();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => ['onCommand', 255],
            ConsoleEvents::TERMINATE => ['onTerminate', -255],
        ];
    }

    protected function write(array $record): void
    {
        $output = $this->output;

        if ($record['level'] >= Logger::ERROR && $output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        $output->write((string) $record['formatted'], false, $this->output->getVerbosity());
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        $formatter = new ConsoleFormatter();

        $formatter->ignoreEmptyContextAndExtra();

        return $formatter;
    }

    private function updateLevel(): bool
    {
        if (null === $this->output) {
            return false;
        }

        $verbosity = $this->output->getVerbosity();
        if (isset($this->verbosityLevelMap[$verbosity])) {
            $this->setLevel($this->verbosityLevelMap[$verbosity]);
        } else {
            $this->setLevel(Logger::DEBUG);
        }

        return true;
    }
}
