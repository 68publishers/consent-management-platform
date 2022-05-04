<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Translator;

use Nette\Utils\Strings;
use Nette\Localization\Translator;

final class TranslatorProxy implements Translator
{
	private Translator $translator;

	/**
	 * @param \Nette\Localization\Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function translate($message, ...$parameters): string
	{
		if (is_string($message) && Strings::startsWith($message, 'ublaboo_datagrid')) {
			$message = '//' . $message;
		}

		return $this->translator->translate($message, ...$parameters);
	}
}
