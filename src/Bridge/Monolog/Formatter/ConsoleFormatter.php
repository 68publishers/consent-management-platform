<?php

declare(strict_types=1);

namespace App\Bridge\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

final class ConsoleFormatter extends LineFormatter
{
    public const SIMPLE_FORMAT = "%start_tag%[%datetime%] %level_name%:%end_tag% %message% %context% %extra%\n";

    public function format(array $record): string
    {
        if ($record['level'] >= Logger::ERROR) {
            $record['start_tag'] = '<error>';
            $record['end_tag'] = '</error>';
        } elseif ($record['level'] >= Logger::NOTICE) {
            $record['start_tag'] = '<comment>';
            $record['end_tag'] = '</comment>';
        } elseif ($record['level'] >= Logger::INFO) {
            $record['start_tag'] = '<info>';
            $record['end_tag'] = '</info>';
        } else {
            $record['start_tag'] = '';
            $record['end_tag'] = '';
        }

        return parent::format($record);
    }
}
