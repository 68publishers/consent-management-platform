<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use App\ReadModel\AbstractDataGridQuery;

final class ConsentsDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @return $this
	 */
	public static function create(string $projectId): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
		]);
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->getParam('project_id');
	}
}
