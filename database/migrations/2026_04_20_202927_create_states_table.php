<?php

declare(strict_types=1);

use App\Libraries\EagerPopulation\Abstracts\EagerPopulatorMigration;
use App\Libraries\EagerPopulation\JsonEagerPopulation;
use App\Models\State;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Libraries\Enums\TimestampsColumnsEnum;

return new class extends EagerPopulatorMigration
{
    public function __construct()
    {
        parent::__construct(
            State::class,
            new JsonEagerPopulation(
                TimestampsColumnsEnum::CREATED_AT,
                ['db' => 'acronym', 'api' => config('database.states.columns.acronym')],
                ['db' => 'name', 'api' => config('database.states.columns.name')],
            )
        );
    }

    protected function getApiUrl(): string
    {
        $statesApiUrl = config('database.states.source');
        if (!$statesApiUrl) {
            throw new \Exception('Api de estados não declarada', 1);
        }
        return $statesApiUrl;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->char('acronym', 2)->nullable(FALSE);
            $table->string('name', 30)->nullable(FALSE);
            $table->timestamp('created_at');
        });
        $this->insertBatch($this->getApiUrl());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
