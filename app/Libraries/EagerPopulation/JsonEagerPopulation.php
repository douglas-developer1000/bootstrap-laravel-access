<?php

declare(strict_types=1);

namespace App\Libraries\EagerPopulation;

use App\Libraries\EagerPopulation\Contracts\PopulationDataMakerInterface;
use App\Libraries\Enums\TimestampsColumnsEnum;

final class JsonEagerPopulation implements PopulationDataMakerInterface
{
    /**
     * Columns keys on format:
     *
     * @var array<int, array{db: string, api: string}> $columns
     */
    protected array $columns;

    public function __construct(
        protected TimestampsColumnsEnum $timestampType,
        array ...$columns
    ) {
        $this->columns = $columns;
    }

    /**
     * Reduce callback that build the array with tuples data to
     * insert them into database
     * 
     * @return array<int, array<string, string>>
     */
    protected function reduceCallbackJson(array $acc, array $next)
    {
        $data = [];
        foreach ($this->columns as $column) {
            ['db' => $bdKey, 'api' => $apiKey] = $column;
            $data[$bdKey] = $next[$apiKey];
        }

        if ($this->timestampType === TimestampsColumnsEnum::CREATED_AT) {
            $data['created_at'] = now();
        } else if ($this->timestampType === TimestampsColumnsEnum::UPDATED_AT) {
            $data['updated_at'] = now();
        } else if ($this->timestampType === TimestampsColumnsEnum::BOTH) {
            $now = now();
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }
        $acc[] = $data;
        return $acc;
    }

    public function findAll(string $url): array
    {
        $handle = \fopen($url, 'r');

        if (!$handle) {
            throw new \Exception("Impossibilidade de Requisição de Json", 1);
        }
        // Read the entire response
        $response = stream_get_contents($handle);
        fclose($handle);

        // Decode JSON into associative array
        return collect(
            json_decode(json: $response, associative: TRUE)
        )->reduce(
            fn($acc, $next) => $this->reduceCallbackJson($acc, $next),
            []
        );
    }
}
