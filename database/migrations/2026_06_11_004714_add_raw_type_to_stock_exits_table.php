<?php

use App\Libraries\Enums\StockExitTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $values = [
                StockExitTypeEnum::SALE->value,
                StockExitTypeEnum::EXCHANGE->value,
                StockExitTypeEnum::DEMONSTRATION->value,
                StockExitTypeEnum::PERSONAL_USE->value,
                StockExitTypeEnum::LOSS->value,
            ];
            $table->enum('type', $values)->nullable(FALSE)->change();
        });
    }
};
