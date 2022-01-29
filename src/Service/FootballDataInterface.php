<?php

namespace App\Service;

interface FootballDataInterface
{
    public function fetchData(string $uri, array $filters = []);
}
