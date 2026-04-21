<?php

declare(strict_types=1);

namespace App\Libraries\EagerPopulation\Contracts;

interface PopulationDataMakerInterface
{
    /**
     * Request and mount the data list used to eager populate items into database
     *
     * @return array<int, array<string, string>>
     */
    public function findAll(string $url): array;
}
