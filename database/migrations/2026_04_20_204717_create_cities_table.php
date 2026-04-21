<?php

declare(strict_types=1);

use App\Libraries\EagerPopulation\Abstracts\EagerPopulatorMigration;
use App\Libraries\EagerPopulation\JsonEagerPopulation;
use App\Models\City;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Libraries\Enums\TimestampsColumnsEnum;

return new class extends EagerPopulatorMigration
{
    public function __construct()
    {
        parent::__construct(
            City::class,
            new JsonEagerPopulation(
                TimestampsColumnsEnum::CREATED_AT,
                ['db' => 'name', 'api' => config('database.cities.columns.name')],
                ['db' => 'state_id', 'api' => config('database.cities.columns.state_id')],
            )
        );
    }

    protected function getApiUrl(): string
    {
        $citiesApiUrl = config('database.cities.source');
        if (!$citiesApiUrl) {
            throw new \Exception('Api de cidades não declarada', 1);
        }
        return $citiesApiUrl;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable(FALSE);
            $table->foreignId('state_id')->constrained();
            $table->timestamp('created_at');
        });
        $this->insertBatch($this->getApiUrl());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
