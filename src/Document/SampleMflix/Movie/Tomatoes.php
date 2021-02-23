<?php

namespace App\Document\SampleMflix\Movie;

use App\Document\SampleMflix\Movie\Tomatoes\Rating;
use App\MongoDB\TypeMapAware;
use App\MongoDB\TypeMapGenerator;
use DateTimeImmutable;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;

final class Tomatoes implements Serializable, Unserializable, TypeMapAware
{
    private const TYPEMAP = [
        'root' => self::class,
        'fieldPaths' => [
            'viewer' => Rating::class,
            'critic' => Rating::class,
        ],
    ];

    private ?int $fresh;
    private ?int $rotten;
    private DateTimeImmutable $lastUpdated;
    private ?string $dvd;
    private ?string $production;
    private ?string $consensus;
    private ?string $boxOffice;

    private ?Rating $viewer;
    private ?Rating $critic;

    public function bsonSerialize(): array
    {
        return [];
    }

    public function bsonUnserialize(array $data)
    {
        $this->fresh = $data['fresh'] ?? null;
        $this->rotten = $data['rotten'] ?? null;
        $this->viewer = $data['viewer'] ?? null;
        $this->critic = $data['critic'] ?? null;
        $this->lastUpdated = DateTimeImmutable::createFromMutable($data['lastUpdated']->toDateTime());
    }

    public static function getTypemap(): array
    {
        return TypeMapGenerator::expandFieldPaths(self::TYPEMAP);
    }
}
