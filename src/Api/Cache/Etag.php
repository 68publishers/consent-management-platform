<?php

declare(strict_types=1);

namespace App\Api\Cache;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

final class Etag
{
    private string $value;

    private function __construct() {}

    public static function fromValidator(string $value, bool $weak = false): self
    {
        if (0 !== strncmp($value, '"', 1)) {
            $value = '"' . $value . '"';
        }

        $etag = new self();
        $etag->value = ($weak ? 'W/' : '') . $value;

        return $etag;
    }

    public static function fromHeader(string $value): self
    {
        $etag = new self();
        $etag->value = $value;

        return $etag;
    }

    public function addToResponse(ApiResponse $response): ApiResponse
    {
        return $response->withHeader('Etag', $this->value);
    }

    public function isNotModified(ApiRequest $request): bool
    {
        if (!in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return false;
        }

        $ifNoneMatch = $request->getHeader('If-None-Match');

        if (0 >= count($ifNoneMatch)) {
            return false;
        }

        $currentEtag = $this->value;

        if (0 === strncmp($currentEtag, 'W/', 2)) {
            $currentEtag = substr($currentEtag, 2);
        }

        foreach ($ifNoneMatch as $etag) {
            if (0 === strncmp($etag, 'W/', 2)) {
                $etag = substr($etag, 2);
            }

            if ($etag === $currentEtag || '*' === $etag) {
                return true;
            }
        }

        return false;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
