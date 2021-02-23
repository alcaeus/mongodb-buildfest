<?php

namespace App\Document\SampleMflix\Movie\Tomatoes;

use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;

final class Rating implements Serializable, Unserializable
{
    public function __construct(
        private float $rating,
        private int $numReviews,
        private int $meter,
    ) {}

    public function bsonSerialize(): array
    {
        return [
            'rating' => $this->rating,
            'numReviews' => $this->numReviews,
            'meter' => $this->meter
        ];
    }

    public function bsonUnserialize(array $data): void
    {
        $this->rating = $data['rating'];
        $this->numReviews = $data['numReviews'];
        $this->meter = $data['meter'];
    }
}
