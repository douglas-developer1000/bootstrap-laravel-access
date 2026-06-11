<?php

use App\Libraries\Enums\StockExitTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            $table->enum(
                'type',
                array_column(StockExitTypeEnum::cases(), 'value')
            )->nullable(FALSE)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            $table->enum(
                'type',
                $this->removeRawCase()->map(
                    fn(BackedEnum $enum) => $enum->value
                )->all()
            )->nullable(FALSE)->change();
        });
    }

    protected function removeRawCase(): Collection
    {
        return collect(StockExitTypeEnum::cases())->filter(
            fn(BackedEnum $enum) => $enum !== StockExitTypeEnum::RAW
        );
    }
};
