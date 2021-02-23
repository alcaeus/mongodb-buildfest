<?php

namespace App\Document\SampleAnalytics;

use BadMethodCallException;
use DateTimeImmutable;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;
use function sprintf;

/**
 * Note: as our data does not include a __pclass field, we're not explicitly
 * implementing the MongoDB\BSON\Persistable interface
 */
final class Customer implements Serializable, Unserializable
{
    public const TYPEMAP = ['root' => CustomerBirthdaysResult::class];

    private ObjectId $id;

    public function __construct(
        private string $name,
        private string $email,
        private DateTimeImmutable $birthday,
    ) {
        $this->id = new ObjectId();
    }

    public function bsonUnserialize(array $data)
    {
        if (isset($this->id)) {
            throw new BadMethodCallException(sprintf('Object of type "%s" is already initialised', self::class));
        }

        $this->id = $data['_id'] ?? new ObjectId();
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->birthday = DateTimeImmutable::createFromMutable($data['birthday']->toDateTime());
    }

    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'birthday' => new UTCDateTime($this->birthday),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getBirthday(): DateTimeImmutable
    {
        return $this->birthday;
    }
}
