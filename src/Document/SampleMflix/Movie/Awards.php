<?php

namespace App\Document\SampleMflix\Movie;

use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;

final class Awards implements Serializable, Unserializable
{
    public function __construct(
        private int $wins = 0,
        private int $nominations = 0,
        private string $text = '',
    ) {}

    public function getText(): string
    {
        return $this->text;
    }

    public function getWins(): int
    {
        return $this->wins;
    }

    public function getNominations(): int
    {
        return $this->nominations;
    }

    public function bsonSerialize(): array
    {
        return [
            'wins' => $this->wins,
            'nominations' => $this->nominations,
            'text' => $this->text,
        ];
    }

    public function bsonUnserialize(array $data): void
    {
        $this->wins = (int) $data['wins'] ?? 0;
        $this->nominations = (int) $data['nominations'] ?? 0;
        $this->text = $data['text'] ?? '';
    }
}
