<?php

declare(strict_types=1);

namespace App\Api\Cache;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

final class Etag
{
	private string $value;

	private function __construct()
	{
	}

	/**
	 * @param string $value
	 * @param bool   $weak
	 *
	 * @return static
	 */
	public static function fromValidator(string $value, bool $weak = FALSE): self
	{
		if (0 !== strncmp($value, '"', 1)) {
			$value = '"' . $value . '"';
		}

		$etag = new self();
		$etag->value = ($weak ? 'W/' : '') . $value;

		return $etag;
	}

	/**
	 * @param string $value
	 *
	 * @return static
	 */
	public static function fromHeader(string $value): self
	{
		$etag = new self();
		$etag->value = $value;

		return $etag;
	}

	/**
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function addToResponse(ApiResponse $response): ApiResponse
	{
		return $response->withHeader('Etag', $this->value);
	}

	/**
	 * @param \Apitte\Core\Http\ApiRequest $request
	 *
	 * @return bool
	 */
	public function isNotModified(ApiRequest $request): bool
	{
		if (!in_array($request->getMethod(), ['GET', 'HEAD'], TRUE)) {
			return FALSE;
		}

		$ifNoneMatch = $request->getHeader('If-None-Match');

		if (0 >= count($ifNoneMatch)) {
			return FALSE;
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
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->value;
	}
}
