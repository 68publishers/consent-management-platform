<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Locale;
use DomainException;
use Throwable;

final class InvalidTemplateException extends DomainException
{
    private ProjectId $projectId;

    private Locale $locale;

    private function __construct(ProjectId $projectId, Locale $locale, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->projectId = $projectId;
        $this->locale = $locale;
    }

    public static function fromPrevious(ProjectId $projectId, Locale $locale, Throwable $e): self
    {
        return new self($projectId, $locale, $e->getMessage(), $e->getCode(), $e);
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function locale(): Locale
    {
        return $this->locale;
    }
}
