<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

trait ConvertibleTimezoneDateFilterTrait
{
    private string $timezoneFrom = 'UTC';

    private string $timezoneTo = 'UTC';

    public function setTimezoneFrom(string $timezone): self
    {
        $this->timezoneFrom = $timezone;

        return $this;
    }

    public function setTimezoneTo(string $timezone): self
    {
        $this->timezoneTo = $timezone;

        return $this;
    }

    public function getTimezoneFrom(): string
    {
        return $this->timezoneFrom;
    }

    public function getTimezoneTo(): string
    {
        return $this->timezoneTo;
    }
}
