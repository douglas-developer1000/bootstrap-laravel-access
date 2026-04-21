<?php

declare(strict_types=1);

namespace App\Libraries\EagerPopulation\Abstracts;

use App\Libraries\EagerPopulation\Contracts\PopulationDataMakerInterface;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Eloquent\Model;

abstract class EagerPopulatorMigration extends Migration
{
    public function __construct(
        protected string $model,
        protected PopulationDataMakerInterface $dataMaker
    ) {
        // ...
    }

    public function insertBatch(string $url)
    {
        $batchList = $this->dataMaker->findAll($url);
        $this->loadModel()::insert($batchList);
    }

    /**
     * Get the available model instance.
     */
    protected function loadModel(): Model
    {
        return app($this->model);
    }
}
