<?php

namespace App\Document\SampleAnalytics;

use MongoDB\BSON\Unserializable;

final class CustomerBirthdaysResult implements Unserializable
{
    public const TYPEMAP = [
        'root' => CustomerBirthdaysResult::class,
        'array' => 'array',
        'fieldPaths' => [
            'customerList.$' => Customer::class,
        ],
    ];

    private int $month;
    private int $numberOfBirthdays;
    private array $customers;

    public function bsonUnserialize(array $data)
    {
        $this->month = (int) $data['birthdayMonth'];
        $this->numberOfBirthdays = (int) $data['birthdaysPerMonth'];
        $this->customers = $data['customerList'];
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getNumberOfBirthdays(): int
    {
        return $this->numberOfBirthdays;
    }

    public function getCustomers(): array
    {
        return $this->customers;
    }

    public function getCustomerNames(): array
    {
        return array_map(
            fn (Customer $customer): string => $customer->getName(),
            $this->customers,
        );
    }

    public function getCustomerNamesAndBirthdays(): array
    {
        return array_map(
            fn (Customer $customer): string => sprintf('%s (%s)', $customer->getName(), $customer->getBirthday()->format('d.m')),
            $this->customers,
        );
    }
}
