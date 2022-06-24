<?php

declare(strict_types=1);

namespace App\Application\Cookie;

use stdClass;
use Throwable;
use ErrorException;
use Latte\Loaders\StringLoader;
use Latte\Sandbox\SecurityPolicy;
use Nette\Bridges\ApplicationLatte\LatteFactory;

final class TemplateRenderer implements TemplateRendererInterface
{
	private LatteFactory $latteFactory;

	/**
	 * @param \Nette\Bridges\ApplicationLatte\LatteFactory $latteFactory
	 */
	public function __construct(LatteFactory $latteFactory)
	{
		$this->latteFactory = $latteFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function render(Template $template): string
	{
		$latte = $this->latteFactory->create();

		$latte->setLoader(new StringLoader([
			$template->projectId() => $template->template(),
		]));

		if (TRUE === $template->option(Template::OPTION_NO_CACHE, FALSE)) {
			$latte->setTempDirectory(NULL);
		}

		$policy = SecurityPolicy::createSafePolicy();

		$policy->allowProperties(stdClass::class, SecurityPolicy::ALL);
		$latte->setPolicy($policy);
		$latte->setSandboxMode(TRUE);

		return $this->catchErrors(function () use ($latte, $template) {
			return $latte->renderToString($template->projectId(), [
				'providers' => $template->arguments()->providers(),
				'cookies' => $template->arguments()->cookies(),
			]);
		});
	}

	/**
	 * @param callable $cb
	 *
	 * @return string
	 * @throws \App\Application\Cookie\CompileException
	 */
	private function catchErrors(callable $cb): string
	{
		$previousErrorHandler = set_error_handler(static function (int $severity, string $message, string $file, int $line) {
			$errors = [
				E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
				E_USER_ERROR => 'E_USER_ERROR',
				E_NOTICE => 'E_NOTICE',
			];

			if (isset($errors[$severity])) {
				throw CompileException::fromPrevious(new ErrorException($message, 0, $severity, $file, $line));
			}

			return FALSE;
		});

		try {
			$result = $cb();
		} catch (CompileException $e) {
		} catch (Throwable $e) {
			$e = CompileException::fromPrevious($e);
		} finally {
			if (is_callable($previousErrorHandler)) {
				set_error_handler($previousErrorHandler);
			}

			if (isset($e)) {
				throw $e;
			}

			return $result ?? '';
		}
	}
}
