<?php

namespace App\Libraries\EagerPopulation;

use App\Libraries\EagerPopulation\Contracts\PopulationDataMakerInterface;

final class CsvEagerPopulation implements PopulationDataMakerInterface
{
    public function findAll(string $url): array
    {
        $handle = \fopen($url, 'r');

        if (!$handle) {
            throw new \Exception("Impossibilidade de Requisição de Csv", 1);
        }
        $batchList = [];
        while (($linha = \fgetcsv($handle, 1000, ",")) !== false) {
            $batchList[] = $linha;
        }
        fclose($handle);

        return $batchList;
    }
}
