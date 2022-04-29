<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

use Nette\Application\UI\Form;
use Nette\Application\UI\Control;
use Nepada\FormRenderer\TemplateRendererFactory;

final class FormFactory implements FormFactoryInterface
{
	private TemplateRendererFactory $templateRendererFactory;

	/**
	 * @param \Nepada\FormRenderer\TemplateRendererFactory $templateRendererFactory
	 */
	public function __construct(TemplateRendererFactory $templateRendererFactory)
	{
		$this->templateRendererFactory = $templateRendererFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $options = []): Form
	{
		$form = new Form();

		if (TRUE === ($options[self::OPTION_AJAX] ?? FALSE)) {
			$form->elementPrototype->class('ajax', TRUE);

			$form->onError[] = static function () use ($form) {
				$parent = $form->getParent();

				if ($parent instanceof Control) {
					$parent->redrawControl();
				}
			};
		}

		$form->setRenderer($this->templateRendererFactory->create());

		return $form;
	}
}
