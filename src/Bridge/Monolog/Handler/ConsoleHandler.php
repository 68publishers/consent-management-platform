<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Symfony\Component\Console\ConsoleEvents;
use Monolog\Handler\AbstractProcessingHandler;
use App\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
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

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
	 * @param bool                                                   $bubble
	 * @param array                                                  $verbosityLevelMap
	 */
	public function __construct(OutputInterface $output = NULL, bool $bubble = TRUE, array $verbosityLevelMap = [])
	{
		parent::__construct(Logger::DEBUG, $bubble);

		$this->output = $output;

		if ($verbosityLevelMap) {
			$this->verbosityLevelMap = $verbosityLevelMap;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHandling(array $record): bool
	{
		return $this->updateLevel() && parent::isHandling($record);
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(array $record): bool
	{
		return $this->updateLevel() && parent::handle($record);
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return void
	 */
	public function setOutput(OutputInterface $output): void
	{
		$this->output = $output;
	}

	/**
	 * @return void
	 */
	public function close(): void
	{
		$this->output = NULL;

		parent::close();
	}

	/**
	 * @internal
	 *
	 * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
	 *
	 * @return void
	 */
	public function onCommand(ConsoleCommandEvent $event): void
	{
		$this->setOutput($event->getOutput());
	}

	/**
	 * @internal
	 *
	 * @return void
	 */
	public function onTerminate(): void
	{
		$this->close();
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ConsoleEvents::COMMAND => ['onCommand', 255],
			ConsoleEvents::TERMINATE => ['onTerminate', -255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record): void
	{
		$output = $this->output;

		if ($record['level'] >= Logger::ERROR && $this->output instanceof ConsoleOutputInterface) {
			$output = $output->getErrorOutput();
		}

		$output->write((string) $record['formatted'], FALSE, $this->output->getVerbosity());
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultFormatter(): FormatterInterface
	{
		$formatter = new ConsoleFormatter();

		$formatter->ignoreEmptyContextAndExtra(TRUE);

		return $formatter;
	}

	/**
	 * @return bool
	 */
	private function updateLevel(): bool
	{
		if (NULL === $this->output) {
			return FALSE;
		}

		$verbosity = $this->output->getVerbosity();
		if (isset($this->verbosityLevelMap[$verbosity])) {
			$this->setLevel($this->verbosityLevelMap[$verbosity]);
		} else {
			$this->setLevel(Logger::DEBUG);
		}

		return TRUE;
	}
}
