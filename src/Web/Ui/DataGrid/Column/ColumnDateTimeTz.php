<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Column;

use DateTime;
use DateTimeZone;
use Ublaboo\DataGrid\Column\Column;
use Ublaboo\DataGrid\Exception\DataGridDateTimeHelperException;
use Ublaboo\DataGrid\Row;
use Ublaboo\DataGrid\Utils\DateTimeHelper;

final class ColumnDateTimeTz extends Column
{
    /** @var string  */
    protected $align = 'right';

    private string $format = 'j. n. Y';

    private ?DateTimeZone $timezone = null;

    public function getColumnValue(Row $row)
    {
        $value = parent::getColumnValue($row);

        if (!$value instanceof DateTime) {
            try {
                $value = DateTimeHelper::tryConvertToDateTime($value);
            } catch (DataGridDateTimeHelperException $e) {
                return $value;
            }
        }

        $value->setTimezone($this->getTimezone());

        return $value->format($this->format);
    }

    /**
     * @return $this
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = new DateTimeZone($timezone);

        return $this;
    }

    public function getTimezone(): DateTimeZone
    {
        if (null === $this->timezone) {
            $this->setTimezone('UTC');
        }

        return $this->timezone;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
