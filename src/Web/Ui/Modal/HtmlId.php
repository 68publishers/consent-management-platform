<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

final class HtmlId
{
    private string $id;

    private function __construct() {}

    public static function create(string $id): self
    {
        $htmlId = new self();
        $htmlId->id = $id;

        return $htmlId;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
