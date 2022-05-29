<?php

declare(strict_types=1);

namespace App\Web\Control\Footer;

interface FooterControlFactoryInterface
{
	/**
	 * @return \App\Web\Control\Footer\FooterControl
	 */
	public function create(): FooterControl;
}
