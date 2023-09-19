<?php

namespace App\Dto;

readonly class RoundDto
{
    public function __construct(
        public int|string $name,
        public string $stage,
        public \DateTimeInterface $dateFrom,
        public \DateTimeInterface $dateTo,
        public string $status
    ) {
    }
}
