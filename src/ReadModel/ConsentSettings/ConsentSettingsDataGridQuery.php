<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use App\ReadModel\AbstractDataGridQuery;

final class ConsentSettingsDataGridQuery extends AbstractDataGridQuery
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

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }
}
