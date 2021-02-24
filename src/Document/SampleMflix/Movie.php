<?php

namespace App\Document\SampleMflix;

use App\Document\SampleMflix\Movie\Awards;
use App\Document\SampleMflix\Movie\Imdb;
use App\Document\SampleMflix\Movie\Tomatoes;
use App\MongoDB\TypeMapAware;
use App\MongoDB\TypeMapGenerator;
use BadMethodCallException;
use DateTimeImmutable;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;

final class Movie implements Serializable, Unserializable, TypeMapAware
{
    private const TYPEMAP = [
        'root' => self::class,
        'fieldPaths' => [
            'awards' => Awards::class,
            'imdb' => Imdb::class,
            'tomatoes' => Tomatoes::class,
        ],
    ];

    private ObjectId $id;

    private string $title;
    private ?string $plot = null;
    private ?string $fullplot = null;
    private ?int $runtime;
    private string $type;

    private ?string $poster = null;
    private ?DateTimeImmutable $released = null;
    private ?string $rated = null;

    /** @var string[] */
    private array $genres;
    /** @var string[] */
    private array $cast;
    /** @var string[] */
    private array $directors;
    /** @var string[] */
    private array $writers;
    /** @var string[] */
    private array $languages;
    /** @var string[] */
    private array $countries;

    private Awards $awards;
    private ?Tomatoes $tomatoes = null;
    private ?Imdb $imdb = null;
    private ?int $metacritic = null;

    private int $numComments;
    private DateTimeImmutable $lastUpdated;
    private int $year;

    public function __construct(string $title)
    {
        $this->id = new ObjectId();
        $this->title = $title;
        $this->awards = new Awards();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAwards(): Awards
    {
        return $this->awards;
    }

    public function bsonSerialize(): array
    {
        $serialized = [
            '_id' => $this->id,

            'title' => $this->title,
            'plot' => $this->plot,
            'fullplot' => $this->fullplot,
            'runtime' => $this->runtime,
            'type' => $this->type,

            'poster' => $this->poster,
            'released' => new UTCDateTime($this->released),
            'rated' => $this->rated,

            'genres' => $this->genres,
            'cast' => $this->cast,
            'directors' => $this->directors,
            'writers' => $this->writers,
            'languages' => $this->languages,
            'countries' => $this->countries,

            'awards' => $this->awards,
            'tomatoes' => $this->tomatoes,
            'imdb' => $this->imdb,
            'metacritic' => $this->metacritic,

            'num_mflix_comments' => $this->numComments,
            // @todo convert to string
            //'lastUpdated' => $this->lastUpdated
            'year' => $this->year,
        ];

        // Don't serialise unset or empty fields
        return array_filter(
            $serialized,
            static fn (mixed $field): bool => !is_null($field) && $field !== []
        );
    }

    public function bsonUnserialize(array $data): void
    {
        if (isset($this->id)) {
            throw new BadMethodCallException();
        }

        $this->id = $data['_id'];

        $this->title = $data['title'];
        $this->plot = $data['plot'] ?? null;
        $this->fullplot = $data['fullplot'] ?? null;
        $this->runtime = $data['runtime'] ?? null;
        $this->type = $data['type'];

        $this->poster = $data['poster'] ?? null;
        $this->released = isset($data['released']) ? DateTimeImmutable::createFromMutable($data['released']->toDateTime()) : null;
        $this->rated = $data['rated'] ?? null;

        // @todo ensure elements are strings
        $this->genres = $data['genres'] ?? [];
        $this->cast = $data['cast'] ?? [];
        $this->directors = $data['directors'] ?? [];
        $this->writers = $data['writers'] ?? [];
        $this->languages = $data['languages'] ?? [];
        $this->countries = $data['countries'] ?? [];

        $this->awards = $data['awards'] ?? new Awards();
        $this->tomatoes = $data['tomatoes'] ?? null;
        $this->imdb = $data['imdb'] ?? null;
        $this->metacritic = $data['metacritic'] ?? null;

        $this->numComments = $data['num_mflix_comments'] ?? 0;
        // @todo parse date string
//        $this->lastUpdated = DateTimeImmutable::createFromFormat()
        $this->year = (int) $data['year'];
    }

    public static function getTypemap(): array
    {
        return TypeMapGenerator::expandFieldPaths(self::TYPEMAP);
    }
}
