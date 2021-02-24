<?php

namespace App\Document\SampleMflix;

use App\Document\SampleMflix\Movie\Awards;
use App\MongoDB\TypeMapAware;
use App\MongoDB\TypeMapGenerator;
use MongoDB\BSON\Unserializable;

final class Director implements Unserializable, TypeMapAware
{
    private const TYPEMAP = [
        'root' => self::class,
        'fieldPaths' => [
            'awards' => Awards::class,
            'mostAwardedMovie' => Movie::class,
        ],
    ];

    private string $director;
    private Awards $awards;
    private Movie $mostAwardedMovie;

    public function getDirector(): string
    {
        return $this->director;
    }

    public function getAwards(): Awards
    {
        return $this->awards;
    }

    public function getMostAwardedMovie(): Movie
    {
        return $this->mostAwardedMovie;
    }

    private function __construct() {}

    public function bsonUnserialize(array $data)
    {
        $this->director = $data['director'];
        $this->awards = $data['awards'];
        $this->mostAwardedMovie = $data['mostAwardedMovie'];
    }

    public static function getTypeMap(): array
    {
        return TypeMapGenerator::expandFieldPaths(self::TYPEMAP);
    }
}
