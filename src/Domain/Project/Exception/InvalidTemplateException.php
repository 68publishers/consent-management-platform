<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use Throwable;
use DomainException;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Project\ValueObject\ProjectId;

final class InvalidTemplateException extends DomainException
{
	private ProjectId $projectId;

	private Locale $locale;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Shared\ValueObject\Locale     $locale
	 * @param string                                    $message
	 * @param int                                       $code
	 * @param \Throwable|NULL                           $previous
	 */
	private function __construct(ProjectId $projectId, Locale $locale, string $message, int $code = 0, ?Throwable $previous = NULL)
	{
		parent::__construct($message, $code, $previous);

		$this->projectId = $projectId;
		$this->locale = $locale;
	}

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Shared\ValueObject\Locale     $locale
	 * @param \Throwable                                $e
	 *
	 * @return static
	 */
	public static function fromPrevious(ProjectId $projectId, Locale $locale, Throwable $e): self
	{
		return new self($projectId, $locale, sprintf(
			'Can\'t render template for project %s and locale %s',
			$projectId->toString(),
			$locale->value()
		), $e->getCode(), $e);
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Locale
	 */
	public function locale(): Locale
	{
		return $this->locale;
	}
}
