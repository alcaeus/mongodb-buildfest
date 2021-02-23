<?php

namespace App\Document\SampleMflix\Movie;

use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;

final class Imdb implements Serializable, Unserializable
{
    public function __construct(
        private int $id,
        private int $votes,
        private float $rating,
    ) {}

    public function bsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'votes' => $this->votes,
            'rating' => $this->rating,
        ];
    }

    public function bsonUnserialize(array $data)
    {
        $this->id = $data['id'];
        $this->votes = $data['votes'];
        $this->rating = $data['rating'];
    }
}
